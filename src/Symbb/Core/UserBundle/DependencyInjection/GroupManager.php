<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\UserBundle\DependencyInjection;

use Symbb\Core\SystemBundle\Manager\AbstractManager;
use \Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use \Doctrine\ORM\EntityManager;
use \Symbb\Core\UserBundle\Entity\GroupInterface;

class GroupManager extends AbstractManager
{

    /**
     * @var string 
     */
    protected $groupClass = '';


    public function __construct($container)
    {
        $config = $container->getParameter('symbb_config');
        $this->config = $config['groupmanager'];
        $this->groupClass = $this->config['group_class'];

    }

    /**
     * update the given group
     * @param \Symbb\Core\UserBundle\Entity\GroupInterface $group
     */
    public function update(GroupInterface $group)
    {
        $this->em->persist($group);
        $this->em->flush();
        return true;

    }

    /**
     * remove the given group
     * @param \Symbb\Core\UserBundle\Entity\GroupInterface $user
     */
    public function remove(GroupInterface $group)
    {
        $this->em->remove($group);
        $this->em->flush();
        return true;

    }

    /**
     * create a new Group
     * @return \Symbb\Core\UserBundle\Entity\GroupInterface
     */
    public function create()
    {
        $groupClass = $this->groupClass;
        $group = new $groupClass();
        return $group;

    }

    /**
     * 
     * @param type $groupId
     * @return \Symbb\Core\UserBundle\Entity\GroupInterface
     */
    public function find($groupId)
    {
        $group = $this->em->getRepository($this->groupClass)->find($groupId);
        return $group;

    }

    /**
     * 
     * @return array(<"\Symbb\Core\UserBundle\Entity\GroupInterface">)
     */
    public function findAll($limit = 20, $page = 1)
    {
        $dql = "SELECT g FROM SymbbCoreUserBundle:Group g";
        $query = $this->em->createQuery($dql);
        $pagination = $this->createPagination($query, $page/* page number */, $limit);
        return $pagination;
    }

    public function getClass()
    {
        return $this->groupClass;

    }
    
}