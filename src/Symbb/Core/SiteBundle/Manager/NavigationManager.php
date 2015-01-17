<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SiteBundle\Manager;

use Symbb\Core\SiteBundle\Entity\Navigation;
use Symbb\Core\SiteBundle\Entity\Site;
use Symbb\Core\SystemBundle\Manager\AbstractManager;

class NavigationManager extends AbstractManager
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher
     */
    protected $dispatcher;

    /**
     *
     * @param type $em
     */
    public function __construct($em, $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param int $id
     * @return Navigation
     */
    public function find($id)
    {
        $object = $this->em->getRepository('SymbbCoreSiteBundle:Navigation')->find($id);
        return $object;
    }

    /**
     * @return Object $objects KNP Paginator
     */
    public function findAll($page = 1, $limit = 20)
    {
        $qb = $this->em->getRepository('SymbbCoreSiteBundle:Navigation')->createQueryBuilder('n');
        $qb->select("n");
        $qb->leftJoin('n.items', 'i');
        $qb->where("i.parentItem IS NULL");
        $qb->orderBy("n.id", "DESC");
        $query = $qb->getQuery();
        $objects = $this->createPagination($query, $page, $limit);
        return $objects;
    }

    /**
     * @param Navigation $object
     * @return bool
     */
    public function save(Navigation $object)
    {
        //@todo validate entity
        $this->em->persist($object);
        $this->em->flush();
        return true;
    }

    /**
     * @param Navigation $object
     * @return bool
     */
    public function remove(Navigation $object)
    {
        $this->em->remove($object);
        $this->em->flush();
        return true;
    }


    /**
     * @param int $id
     * @return Navigation\Item
     */
    public function findItem($id)
    {
        $object = $this->em->getRepository('SymbbCoreSiteBundle:Navigation\Item')->find($id);
        return $object;
    }


    /**
     * @param Navigation $navigation
     * @return object $objects KNP Paginator
     */
    public function findAllItems(Navigation $navigation, $page = 1, $limit = 20)
    {
        $qb = $this->em->getRepository('SymbbCoreSiteBundle:Navigation\Item')->createQueryBuilder('i');
        $qb->select("i");
        $qb->where("i.navigation = :navgation");
        $qb->setParameter('navgation', $navigation);
        $qb->orderBy("i.position", "DESC");
        $query = $qb->getQuery();
        $objects = $this->createPagination($query, $page, $limit);
        return $objects;
    }

    /**
     * @param Navigation\Item $object
     * @return bool
     */
    public function saveItem(Navigation\Item $object)
    {
        //@todo validate entity
        $this->em->persist($object);
        $this->em->flush();
        return true;
    }

    /**
     * @param Navigation\Item $object
     * @return bool
     */
    public function removeItem(Navigation\Item $object)
    {
        $this->em->remove($object);
        $this->em->flush();
        return true;
    }
}
