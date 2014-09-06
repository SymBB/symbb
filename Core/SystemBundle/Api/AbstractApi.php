<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SystemBundle\Api;

use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\Query;
use SymBB\Core\MessageBundle\DependencyInjection\MessageManager;
use SymBB\Core\SystemBundle\DependencyInjection\AccessManager;
use SymBB\Core\UserBundle\DependencyInjection\UserManager;
use SymBB\Core\UserBundle\Entity\UserInterface;
use Symfony\Component\Translation\Translator;
use \Doctrine\ORM\EntityManager;
use \Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractApi
{

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var AccessManager
     */
    protected$accessManager;

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var
     */
    protected $paginator;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var MessageManager
     */
    protected $messageManager;

    protected $serializer;

    /**
     * @var array
     */
    protected static $messages = array();

    /**
     * @var array
     */
    protected static $callbacks = array();

    /**
     * @var array
     */
    protected static $breadcrumbItems = array();

    /**
     * @var array
     */
    protected static $paginationData = array();

    /**
     * @var array
     */
    protected static $success = true;

    /**
     * @param AccessManager $manager
     */
    public function setAccessManager(AccessManager $manager){
        $this->accessManager = $manager;
    }

    /**
     * @param UserManager $manager
     */
    public function setUserManager(UserManager $manager){
        $this->userManager = $manager;
    }

    /**
     * @param MessageManager $manager
     */
    public function setMessageManager(MessageManager $manager){
        $this->messageManager = $manager;
    }

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher){
        $this->eventDispatcher = $eventDispatcher;
    }


    /**
     * @param $paginator
     */
    public function setPaginator($paginator){
        $this->paginator = $paginator;
    }

    /**
     * @param Translator $translator
     */
    public function setTranslator(Translator $translator){
        $this->translator = $translator;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function setEntityManager(EntityManager $em){
        $this->em = $em;
    }

    /**
     *
     * @return UserInterface
     */
    public function getUser()
    {
        if (!is_object($this->user)) {
            $this->user = $this->userManager->getCurrentUser();
        }
        return $this->user;

    }

    /**
     * @param $extension
     * @param $access
     * @param $identity
     * @return bool
     */
    public function checkAccess($extension, $access, $identity){
        $this->accessManager->addAccessCheck($extension, $access, $identity);
        return $this->accessManager->hasAccess();
    }

    /**
     * @param $query
     * @param $page
     * @param $limit
     * @return mixed
     */
    public function createPagination($query, $page, $limit){

        $rsm = new ResultSetMappingBuilder($this->em);
        $rsm->addScalarResult('count', 'count');

        $queryCount = $query->getSql();
        $queryCount = "SELECT COUNT(*) as count FROM (".$queryCount.") as temp";
        $queryCount = $this->em->createNativeQuery($queryCount, $rsm);
        $queryCount->setParameters($query->getParameters());
        $count = $queryCount->getSingleScalarResult();
        if(!$count){
            $count = 0;
        }

        if($page === 'last'){
            $page = $count / $limit;
            $page = ceil($page);
        }

        if($page <= 0){
            $page = 1;
        }

        $query->setHint('knp_paginator.count', $count);

        $pagination = $this->paginator->paginate(
            $query, (int)$page, $limit, array('distinct' => false)
        );

        return $pagination;
    }

    /**
     * @param \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination $pagination
     */
    public function addPaginationData(\Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination $pagination)
    {
        if (empty(static::$paginationData)) {
            static::$paginationData = $pagination->getPaginationData();
        }
    }

    /**
     * @param $callbackName
     */
    public function addCallback($callbackName)
    {
        static::$callbacks[] = $callbackName;
    }

    /**
     * @param array $breadbrumb
     */
    public function addBreadcrumbItems($breadbrumb)
    {
        static::$breadcrumbItems = $breadbrumb;
    }

    /**
     * @param array $params
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getJsonResponse($params = array())
    {
        $user = $this->getUser();
        $authenticated = false;
        if ($user->getSymbbType() === 'user') {
            $authenticated = true;
        }
        if (!isset($params['user'])) {
            $params['user'] = array();
        }
        $params['user']['id'] = $user->getId();
        $params['user']['username'] = $user->getUsername();
        $params['user']['type'] = $user->getSymbbType();
        $params['user']['authenticated'] = $authenticated;
        $params['user']['count'] = array(
            'newMessages' => $this->messageManager->countNewMessages()
        );
        $params['messages'] = $this->messages;
        $params['callbacks'] = static::$callbacks;
        $params['breadcrumbItems'] = static::$breadcrumbItems;
        $params['success'] = static::$success;
        if (!empty(static::$paginationData)) {
            $params['paginationData'] = static::$paginationData;
        }
        $response = new \Symfony\Component\HttpFoundation\Response(json_encode($params));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * get a ISO8601 String ( Date with Timezone and Time Information )
     * @param \DateTime $datetime
     * @return null|string
     */
    public function getISO8601ForUser(\DateTime $datetime = null)
    {
        if ($datetime) {
            $datetime->setTimezone($this->userManager->getTimezone());
            return $datetime->format(\DateTime::ISO8601);
        }

        return null;
    }

    /**
     * add a error message to the api call
     * @param $message
     */
    public function addErrorMessage($message)
    {
        static::$messages[] = array(
            'type' => 'error',
            'bootstrapType' => 'danger',
            'message' => $this->trans($message)
        );
        static::$success = false;
    }

    /**
     * add a success message to the api call
     * @param $message
     */
    public function addSuccessMessage($message)
    {
        static::$messages[] = array(
            'type' => 'success',
            'bootstrapType' => 'success',
            'message' => $this->trans($message)
        );
    }

    /**
     * add a info message to the api call
     * @param $message
     */
    public function addInfoMessage($message)
    {
        static::$messages[] = array(
            'type' => 'info',
            'bootstrapType' => 'info',
            'message' => $this->trans($message)
        );
    }

    /**
     * add a warning message to the api
     * @param $message
     */
    public function addWarningMessage($message)
    {
        static::$messages[] = array(
            'type' => 'warning',
            'bootstrapType' => 'warning',
            'message' => $this->trans($message)
        );
    }

    /**
     * check if some error messages was added to the api call
     * @return bool
     */
    public function hasError()
    {
        foreach (static::$messages as $message) {
            if ($message['type'] === 'error') {
                return true;
            }
        }
        return false;
    }

    /**
     * translate a string
     * @param $msg
     * @param array $param
     * @return mixed
     */
    public function trans($msg, $param = array())
    {
        return $this->translator->trans($msg, $param, 'symbb_frontend');
    }

    public function setSerializer($serializer){
        $this->serializer = $serializer;
    }

    /**
     * @param object|array $object
     * @return array
     */
    public function createArrayOfObject($object){
        $json = $this->serializer->serialize($object, 'json');
        $array = json_decode($json, 1);
        return $array;
    }
}
