<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SystemBundle\Api;

use Doctrine\ORM\Query;
use Symbb\Core\MessageBundle\DependencyInjection\MessageManager;
use Symbb\Core\SystemBundle\Manager\AccessManager;
use Symbb\Core\UserBundle\Manager\UserManager;
use Symbb\Core\UserBundle\Entity\UserInterface;
use Symfony\Component\Translation\TranslatorInterface;
use \Doctrine\ORM\EntityManager;
use \Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

abstract class AbstractApi
{
    const INFO_NO_ENTRIES_FOUND = 'no entries found';
    const INFO_UNKNOWN_FIELD = 'Field "%field%" is unknown';
    const ERROR_ENTRY_NOT_FOUND = 'Entry not found';
    const SUCCESS_SAVED = 'successfully saved';
    const SUCCESS_DELETED = 'successfully deleted';

    /**
     * in backend we will disable the access checks
     * @var bool
     */
    public $entityAccessCheck = true;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var AccessManager
     */
    protected $accessManager;

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var
     */
    protected $paginator;

    /**
     * @var TranslatorInterface
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
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator){
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
        $params['messages'] = static::$messages;
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
    public function addErrorMessage($message, $params = array())
    {
        static::$messages[] = array(
            'type' => 'error',
            'bootstrapType' => 'danger',
            'message' => $this->trans($message, $params)
        );
        static::$success = false;
    }

    /**
     * add a success message to the api call
     * @param $message
     */
    public function addSuccessMessage($message, $params = array())
    {
        static::$messages[] = array(
            'type' => 'success',
            'bootstrapType' => 'success',
            'message' => $this->trans($message, $params)
        );
    }

    /**
     * add a info message to the api call
     * @param $message
     */
    public function addInfoMessage($message, $params = array())
    {
        static::$messages[] = array(
            'type' => 'info',
            'bootstrapType' => 'info',
            'message' => $this->trans($message, $params)
        );
    }

    /**
     * add a warning message to the api
     * @param $message
     */
    public function addWarningMessage($message, $params = array())
    {
        static::$messages[] = array(
            'type' => 'warning',
            'bootstrapType' => 'warning',
            'message' => $this->trans($message, $params)
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
        $array = array();
        $fields = $this->getFieldsForObject($object, 'toArray');
        if($object){
            if(!$fields || empty($fields)){
                $json = $this->serializer->serialize($object, 'json');
                $array = json_decode($json, 1);
            } else {
                foreach($fields as $field){
                    $method = '';
                    $parts = explode('_', $field);
                    foreach($parts as $key => $part){
                        $method .= ucfirst($part);
                    }
                    $getter = 'get'.$method;
                    $is = 'is'.$method;
                    $value = null;
                    if(method_exists($object, $getter)){
                        $value = $object->$getter();
                    } else if(method_exists($object, $is)){
                        $value = $object->$is();
                    }
                    if (
                        $value instanceof ArrayCollection ||
                        $value instanceof PersistentCollection ||
                        is_array($value)
                    ){
                        $newValue = array();
                        foreach($value as $key => $value2){
                            $newValue[$key] = $this->createArrayOfObject($value2);
                        }
                        $value = $newValue;
                    } else if(is_object($value)){
                        $value = $this->createArrayOfObject($value);
                    }
                    $array[$field] = $value;
                }
            }
        }

        return $array;
    }

    abstract protected function getFieldsForObject($object, $direction);

    /**
     * @param object $site
     * @param $data
     * @return object
     */
    public function assignArrayToObject($object, $data){

        $fields = $this->getFieldsForObject($object, 'toObject');

        foreach($fields as $field){
            // only assign if the key is set
            if(isset($data[$field]) && $field !== "id"){
                $setter = 'set';
                $getter = 'get';
                $parts = explode('_', $field);
                foreach($parts as $key => $part){
                    $setter .= ucfirst($part);
                    $getter .= ucfirst($part);
                }

                $test = null;
                if(method_exists($object, $getter)){
                    $test = $object->$getter();
                }
                // test if it is an array or object
                if (
                    $test instanceof ArrayCollection ||
                    $test instanceof PersistentCollection ||
                    is_array($test)
                ){
                    if(!is_array($test)){
                        $newValue = new ArrayCollection();
                    } else {
                        $newValue = array();
                    }
                    foreach($data[$field] as $key => $value2){
                        $subobject = $this->createNewSubobject($object, $field);
                        if($subobject){
                            $newValue[$key] = $this->assignArrayToObject($subobject, $value2);
                        }
                    }
                    $data[$field] = $newValue;
                } else if(is_object($test)){
                    $data[$field] = $this->createArrayOfObject($data[$field]);
                }
                // set the value
                $object->$setter($data[$field]);
            }
        }

        foreach($data as $key => $value){
            if(!in_array($key, $fields) && $key !== 'id'){
                $this->addInfoMessage(self::INFO_UNKNOWN_FIELD, array('%field%' => $key));
            }
        }

        return $object;
    }

    protected function createNewSubobject($parent, $field){
        return null;
    }
}
