<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Api;

use Symbb\Core\ForumBundle\DependencyInjection\ForumManager;
use Symbb\Core\ForumBundle\Entity\Forum;
use Symbb\Core\ForumBundle\Entity\Topic;
use Symbb\Core\SystemBundle\Api\AbstractApi;

class ForumApi extends AbstractApi
{
    const ERROR_WRONG_OBJECT = 'you have passed a wrong object';

    /**
     * @var ForumManager
     */
    protected $forumManager;

    /**
     * @param $id
     * @return Forum
     */
    public function find($id){
        $object = $this->forumManager->find($id);
        if(!is_object($object)){
            $this->addErrorMessage(self::ERROR_ENTRY_NOT_FOUND);
        }
        return $object;
    }

    /**
     * @param $limit
     * @param $page
     * @return Forum[]
     */
    public function findAll($parentId, $limit, $page){
        $objects = $this->forumManager->findAll($parentId, $limit, $page);
        $this->addPaginationData($objects);
        $objects = $objects->getItems();
        if(empty($objects)){
            $this->addInfoMessage(self::INFO_NO_ENTRIES_FOUND);
        }
        return $objects;
    }

    /**
     * save a Forum
     * you can pass the Site object or an array with the fields
     * if you pass an array the keys must be with underscore and not with CamelCase
     * @param Forum|array $object
     * @return Forum
     */
    public function save($object){

        if(is_array($object)){
            $objectData = $object;
            if($object['id'] > 0){
                $object = $this->find($object['id']);
            } else {
                $object = new Forum();
            }
            $this->assignArrayToObject($object, $objectData);
        } else if(!($object instanceof Forum)) {
            $this->addErrorMessage(self::ERROR_WRONG_OBJECT);
        }

        if(!$this->hasError()){
            $check = $this->forumManager->update($object);
            if($check === true){
                $this->addSuccessMessage(self::SUCCESS_SAVED);
            }
        }

        return $object;
    }

    /**
     * @param int|Forum $object
     * @return bool
     */
    public function delete($object){
        if(is_numeric($object)){
            $object = $this->find($object);
        } else if(!($object instanceof Forum)) {
            $this->addErrorMessage(self::ERROR_WRONG_OBJECT);
        }
        if(!$this->hasError()){
            $check = $this->userManager->removeUser($object);
            if($check){
                $this->addSuccessMessage(self::SUCCESS_DELETED);
            }
            return $check;
        }
        return false;
    }

    /**
     * @param $object
     * @param $direction
     * @return array|null
     */
    protected function getFieldsForObject($object, $direction){
        $fields = array();
        if($object instanceof Forum){
            // only this fields are allowed
            $fields = array(
                'id',
                'image_name',
                'type',
                'name',
                'link',
                'countLinkCalls',
                'description',
                'active',
                'show_sub_forum_list',
                'entries_per_page',
                'position'
            );
            if($direction == "toArray"){
                $fields[] = 'children';
            }
        }
        return $fields;
    }

    /**
     * @param ForumManager $manager
     */
    public function setForumManager(ForumManager $manager){
        $this->forumManager = $manager;
    }

}