<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\UserBundle\Api;

use Symbb\Core\SystemBundle\Api\AbstractApi;
use Symbb\Core\UserBundle\DependencyInjection\GroupManager;
use Symbb\Core\UserBundle\Entity\Group;
use Symbb\Core\UserBundle\Entity\GroupInterface;

class GroupApi extends AbstractApi
{
    const ERROR_WRONG_OBJECT = 'you have passed a wrong object';

    /**
     * @var GroupManager
     */
    protected $groupManager;

    /**
     * @param $id
     * @return GroupInterface
     */
    public function find($id){
        $object = $this->groupManager->find($id);
        if(!is_object($object)){
            $this->addErrorMessage(self::ERROR_ENTRY_NOT_FOUND);
        }
        return $object;
    }

    /**
     * @param $limit
     * @param $page
     * @return array
     */
    public function getList($limit, $page){
        $objects = $this->groupManager->findAll($limit, $page);
        $this->addPaginationData($objects);
        $objects = $objects->getItems();
        if(empty($objects)){
            $this->addInfoMessage(self::INFO_NO_ENTRIES_FOUND);
        }
        return $objects;
    }

    /**
     * save a Site
     * you can pass the Site object or an array with the fields
     * if you pass an array the keys must be with underscore and not with CamelCase
     * @param GroupInterface|array $object
     * @return GroupInterface
     */
    public function save($object){

        if(is_array($object)){
            $objectData = $object;
            if($object['id'] > 0){
                $object = $this->find($object['id']);
            } else {
                $object = new Group();
            }
            $this->assignArrayToObject($object, $objectData, $this->getGroupArrayFields());
        } else if(!($object instanceof GroupInterface)) {
            $this->addErrorMessage(self::ERROR_WRONG_OBJECT);
        }

        if(!$this->hasError()){
            $check = $this->groupManager->update($object);
            if($check === true){
                $this->addSuccessMessage(self::SUCCESS_SAVED);
            }
        }

        return $object;
    }

    /**
     * @param int|GroupInterface $object
     * @return bool
     */
    public function delete($object){
        if(is_numeric($object)){
            $object = $this->find($object);
        } else if(!($object instanceof GroupInterface)) {
            $this->addErrorMessage(self::ERROR_WRONG_OBJECT);
        }
        if(!$this->hasError()){
            $check = $this->groupManager->remove($object);
            if($check){
                $this->addSuccessMessage(self::SUCCESS_DELETED);
            }
            return $check;
        }
        return false;
    }

    /**
     * return a list of all field names of the Site object as Array
     * @return array
     */
    public function getGroupArrayFields(){
        // only this fields are allowed
        $fields = array(
            'name'
        );
        return $fields;
    }

    /**
     * @param GroupManager $manager
     */
    public function setGroupManager(GroupManager $manager){
        $this->groupManager = $manager;
    }

}