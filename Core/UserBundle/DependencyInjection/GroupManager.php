<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\UserBundle\DependencyInjection;

use \Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use \Doctrine\ORM\EntityManager;
use \SymBB\Core\UserBundle\Entity\GroupInterface;

class GroupManager
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var SecurityFactoryInterface
     */
    protected $securityFactory;

    /**
     * @var string 
     */
    protected $groupClass = '';

    protected $paginator;

    public function __construct($container)
    {
        $this->em = $container->get('doctrine.orm.symbb_entity_manager');
        $this->securityFactory = $container->get('security.encoder_factory');
        $config = $container->getParameter('symbb_config');
        $this->config = $config['groupmanager'];
        $this->groupClass = $this->config['group_class'];
        $this->paginator = $container->get('knp_paginator');

    }

    /**
     * update the given group
     * @param \SymBB\Core\UserBundle\Entity\GroupInterface $group
     */
    public function updateGroup(GroupInterface $group)
    {
        $this->em->persist($group);
        $this->em->flush();

    }

    /**
     * remove the given group
     * @param \SymBB\Core\UserBundle\Entity\GroupInterface $user
     */
    public function removeUser(GroupInterface $group)
    {
        $this->em->remove($group);
        $this->em->flush();

    }

    /**
     * create a new Group
     * @return \SymBB\Core\UserBundle\Entity\GroupInterface
     */
    public function createGroup()
    {
        $groupClass = $this->groupClass;
        $group = new $groupClass();
        return $group;

    }

    public function findGroups()
    {
        $groups = $this->em->getRepository($this->groupClass)->findAll();
        return $groups;

    }

    public function getClass()
    {
        return $this->groupClass;

    }

    public function paginateAll($request)
    {
        $dql = "SELECT g FROM SymBBCoreUserBundle:Group g";
        $query = $this->em->createQuery($dql);

        $paginator = $this->paginator;
        $pagination = $paginator->paginate(
            $query, $request->query->get('page', 1)/* page number */, 20/* limit per page */
        );

        return $pagination;

    }
    
}