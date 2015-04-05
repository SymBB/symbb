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
        $data["image"] = $object->getImage();
        $targetForum = $object->getTargetForum();
        $data["targetForum"] =  0;
        if($targetForum){
            $data["targetForum"] = $targetForum->getId();
        }
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
                $sourceData["port"] = $source->getPort();
                $sourceData["ssl"] = $source->isSsl();
                $sourceData["type"] = "email";
            } else if($source instanceof Category\Source\Feed){
                $sourceData["url"] = $source->getUrl();
                $sourceData["type"] = "feed";
            }
            $data["sources"][] = $sourceData;
        }
        if(empty($data["sources"])){
            $data["sources"][] = array(
                "id" => 0,
                "name" => ""
            );
        }
        return $data;
    }

    public function assignArrayToObject(Category $object, $data){

        $object->setName($data["name"]);
        $object->setImage($data["image"]);

        if($data["targetForum"]){
            $targetForum = $this->forumManager->find($data["targetForum"]);
            $object->setTargetForum($targetForum);
        }

        $sources = $object->getSources();

        foreach($data["sources"] as $sourceData){
            $newSource = null;
            $oldSource = null;
            $oldSourceId = null;
            foreach($sources as $source){
                if($sourceData["id"] == $source->getId()){
                    $oldSource = $source;
                    break;
                }
            }

            if($sourceData["type"] == "email"){
                if(!($oldSource instanceof Category\Source\Email)){
                    if($oldSource){
                        $this->em->remove($oldSource);
                    }
                    $newSource = new Category\Source\Email();
                } else {
                    $newSource = $oldSource;
                }
                $newSource->setServer($sourceData["server"]);
                $newSource->setUsername($sourceData["username"]);
                $newSource->setPassword($sourceData["password"]);
                $newSource->setSsl((bool)$sourceData["ssl"]);
                $newSource->setPort($sourceData["port"]);
            } else if($sourceData["type"] == "feed") {
                if(!($oldSource instanceof Category\Source\Feed)){
                    if($oldSource){
                        $this->em->remove($oldSource);
                    }
                    $newSource = new Category\Source\Feed();
                } else {
                    $newSource = $oldSource;
                }
                $newSource->setUrl($sourceData["url"]);
            }
            if($newSource){
                $newSource->setName($sourceData["name"]);
                $newSource->setCategory($object);
                $this->em->persist($newSource);
            }
        }

        return $object;
    }

    public function setForumManager($manager){
        $this->forumManager = $manager;
    }
}