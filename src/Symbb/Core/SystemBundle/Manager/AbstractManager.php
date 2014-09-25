<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SystemBundle\Manager;

use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\Query;
use SymBB\Core\UserBundle\DependencyInjection\UserManager;
use SymBB\Core\UserBundle\Entity\UserInterface;
use \Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Translation\Translator;
use \Doctrine\ORM\EntityManager;
use \Symfony\Component\EventDispatcher\EventDispatcherInterface;

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

    public function checkAccess($extension, $access, $identity){
        $this->accessManager->addAccessCheck($extension, $access, $identity);
        return $this->accessManager->hasAccess();
    }


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
}
