<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\UserBundle\Manager;

use Doctrine\ORM\Query;
use Symbb\Core\SystemBundle\Manager\AbstractManager;
use Symbb\Core\UserBundle\Entity\Field;


class FieldManager extends AbstractManager
{


    /**
     * @param Field $object
     * @return bool
     */
    public function update(Field $object)
    {
        $this->em->persist($object);
        $this->em->flush();
        return true;
    }

    /**
     * @param Field $object
     * @return bool
     */
    public function remove(Field $object)
    {
        $this->em->remove($object);
        $this->em->flush();
        return true;
    }

    /**
     * @return Field
     */
    public function create()
    {
        $object = new Field();
        return $object;
    }

    /**
     * 
     * @param int $id
     * @return Field
     */
    public function find($id)
    {
        $user = $this->em->getRepository('SymbbCoreUserBundle:Field')->find($id);
        return $user;
    }

    /**
     * @param int $limit
     * @param int $page
     * @return mixed
     */
    public function findAll($limit = 20, $page = 1)
    {
        $dql = "SELECT f FROM SymbbCoreUserBundle:Field f";
        $query = $this->em->createQuery($dql);
        $pagination = $this->createPagination($query, $page/* page number */, $limit);
        return $pagination;
    }

}