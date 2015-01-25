<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\NewsBundle\Api;

use Symbb\Core\ForumBundle\Entity\Forum;
use Symbb\Core\NewsBundle\Entity\Category;
use Symbb\Core\SystemBundle\Api\AbstractCrudApi;

class CategoryApi extends AbstractCrudApi
{

    protected $forumManager;

    /**
     * @param $object
     * @return bool
     */
    protected function isCorrectInstance($object){
        return $object instanceof Category;
    }

    /**
     * @return Category
     */
    protected function createNewObject(){
        return new Category();
    }

    /**
     * @param $object
     * @param $direction
     * @return array|null
     */
    protected function getFieldsForObject($object, $direction)
    {

        $fields = array();
        if ($object instanceof Category) {
            // only this fields are allowed
            $fields = array(
                'id',
                'name',
            );
        }

        return $fields;
    }

    public function createArrayOfObject($object){
        $data = array();
        $data["id"] = $object->getId();
        $data["name"] = $object->getName();
        $data["targetForum"] = $object->getTargetForum()->getId();
        $data["sources"] = array();
        return $data;
    }

    public function assignArrayToObject(Category $object, $data){

        $targetForum = $this->forumManager->find($data["targetForum"]);

        $object->setName($data["name"]);
        $object->setTargetForum($targetForum);

        return $object;
    }

    public function setForumManager($manager){
        $this->forumManager = $manager;
    }
}