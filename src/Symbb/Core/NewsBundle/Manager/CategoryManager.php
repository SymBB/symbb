<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\NewsBundle\Manager;

use Symbb\Core\NewsBundle\Entity\Category;
use Symbb\Core\SystemBundle\Manager\AbstractManager;

class CategoryManager extends AbstractManager
{

    /**
     * @param int $id
     * @return Category
     */
    public function find($id)
    {
        $site = $this->em->getRepository('SymbbCoreNewsBundle:Category')->find($id);
        return $site;
    }

    /**
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function findAll($page = 1, $limit = 20)
    {
        $qb = $this->em->getRepository('SymbbCoreNewsBundle:Category')->createQueryBuilder('s');
        $qb->select("s");
        $query = $qb->getQuery();
        $objects = $this->createPagination($query, $page, $limit);
        return $objects;
    }

    /**
     * @param Category $category
     * @return bool
     */
    public function save(Category $category)
    {
        $this->em->persist($category);
        $this->em->flush();
        return true;
    }

    /**
     * @param Category $category
     * @return bool
     */
    public function remove(Category $category)
    {
        $this->em->remove($category);
        $this->em->flush();
        return true;
    }
}
