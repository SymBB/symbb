<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SystemBundle\Manager;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\Query;
use Symbb\Core\UserBundle\Manager\UserManager;
use Symbb\Core\UserBundle\Entity\UserInterface;
use \Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;
use \Doctrine\ORM\EntityManager;
use \Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\ValidatorInterface;

abstract class AbstractManager
{

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
     * @var Paginator
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
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var array
     */
    protected $cacheData = array();

    /**
     * @param AccessManager $manager
     */
    public function setAccessManager(AccessManager $manager)
    {
        $this->accessManager = $manager;
    }

    /**
     * @param UserManager $manager
     */
    public function setUserManager(UserManager $manager)
    {
        $this->userManager = $manager;
    }

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }


    /**
     * @param $paginator
     */
    public function setPaginator($paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
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
     * @param Query $query
     * @param $page
     * @param $limit
     * @return mixed
     */
    public function createPagination(Query $query, $page, $limit)
    {

        // in the case of last page
        // we need the count of the query to calculate the last page
        // normaly we can create a own count query and use it instead of paginator counting
        // but the hint setting is net working
        // it is executing also a own count query
        // therefore we will only define a own count query in the case of "last" page to calculate it
        if ($page === 'last') {
            $count = 0;
            $queryCount = $query->getSql();

            if(strpos($queryCount, "GROUP BY") === false){
                $countQuery = clone $query;
                $countQuery->setHint(Query::HINT_CUSTOM_TREE_WALKERS, array('Symbb\Core\SystemBundle\DependencyInjection\CountSqlWalker'));
                $countQuery->setFirstResult(null)->setMaxResults(null);
                $countQuery->setParameters($query->getParameters());
            } else {
                // get sql, get the from part and remove all other fields then the id field
                // so that we have a query who select only one field
                // for count this is better because we dont need the data
                $rsm = new ResultSetMappingBuilder($this->em);
                $rsm->addScalarResult('count', 'count');
                $queryCountTmp = explode("FROM", $queryCount);
                $queryCountSelect = array_shift($queryCountTmp);
                $queryCountEnd = implode("FROM", $queryCountTmp);
                $queryCountSelect = explode(",", $queryCountSelect);
                $queryCountSelect = reset($queryCountSelect);
                $queryCount = "SELECT COUNT(*) as count FROM (" . $queryCountSelect . " FROM " . $queryCountEnd . ") as temp";
                // create now the query based on the native sql query and get the count
                $countQuery = $this->em->createNativeQuery($queryCount, $rsm);
                $countQuery->setParameters($query->getParameters());
            }

            $count = $countQuery->getSingleScalarResult();

            if (!$count) {
                $count = 0;
            }
            // this is not working! therfore make it only in case of "last" page
            // because we need the count
            $query->setHint('knp_paginator.count', $count);

            $page = $count / $limit;
            $page = ceil($page);
        }

        if ($page <= 0) {
            $page = 1;
        }

        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Symbb\Core\SystemBundle\DependencyInjection\MysqlWalker');

        $pagination = $this->paginator->paginate(
            $query, (int)$page, $limit, array('distinct' => false, 'wrap-queries'=>true)
        );

        return $pagination;
    }

    /**
     * @param $key
     * @param $value
     */
    public function setCacheData($key, $value)
    {
        $this->cacheData[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getCacheData($key)
    {
        return $this->cacheData[$key];
    }

    /**
     * @param ValidatorInterface $validator
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }
}
