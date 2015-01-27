<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\NewsBundle\Api;

use Doctrine\Common\Collections\ArrayCollection;
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

    public function createArrayOfObject(Category $object){
        $data = array();
        $data["id"] = $object->getId();
        $data["name"] = $object->getName();
        $data["targetForum"] = $object->getTargetForum()->getId();
        $data["sources"] = array();
        foreach($object->getSources() as $source){
            $sourceData = array(
                'id' =>   $source->getId(),
                'name' => $source->getName()
            );
            if($source instanceof Category\Source\Email){
                $sourceData["server"] = $source->getServer();
                $sourceData["username"] = $source->getUsername();
                $sourceData["password"] = $source->getPassword();
                $sourceData["type"] = "email";
            } else if($source instanceof Category\Source\Feed){
                $sourceData["url"] = $source->getUrl();
                $sourceData["type"] = "feed";
            }
            $data["sources"][] = $sourceData;
        }
        return $data;
    }

    public function assignArrayToObject(Category $object, $data){

        $targetForum = $this->forumManager->find($data["targetForum"]);

        $object->setName($data["name"]);
        $object->setTargetForum($targetForum);
        $object->setSources(new ArrayCollection());

        foreach($data["sources"] as $sourceData){
            $source = null;
            if($sourceData["type"] == "email"){
                $source = new Category\Source\Email();
                $source->setServer($sourceData["server"]);
                $source->setUsername($sourceData["username"]);
                $source->setPassword($sourceData["password"]);
            } else if($sourceData["type"] == "feed") {
                $source = new Category\Source\Feed();
                $source->setUrl($sourceData["url"]);
            }
            if($source){
                $source->setName($sourceData["name"]);
                $source->setCategory($object);
                $object->addSource($source);
            }
        }

        return $object;
    }

    public function setForumManager($manager){
        $this->forumManager = $manager;
    }
}