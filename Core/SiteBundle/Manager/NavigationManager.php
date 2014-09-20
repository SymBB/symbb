<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SiteBundle\Manager;

use SymBB\Core\SiteBundle\Entity\Navigation;
use SymBB\Core\SiteBundle\Entity\Site;

class NavigationManager
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
    public function find($id){
        $object = $this->em->getRepository('SymBBCoreSiteBundle:Navigation')->find($id);
        return $object;
    }

    /**
     * @return array<Navigation>
     */
    public function findAll(){
        $objects = $this->em->getRepository('SymBBCoreSiteBundle:Navigation')->findAll();
        return $objects;
    }

    /**
     * @param Navigation $site
     * @return bool
     */
    public function save(Navigation $object){
        $this->em->persist($object);
        $this->em->flush();
        return true;
    }

    /**
     * @param Site $site
     * @return bool
     */
    public function remove(Navigation $object){
        $this->em->remove($object);
        $this->em->flush();
        return true;
    }
}
