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
use Symbb\Core\UserBundle\DependencyInjection\UserManager;
use Symbb\Core\UserBundle\Entity\User;
use Symbb\Core\UserBundle\Entity\UserInterface;

class UserApi extends AbstractApi
{
    const ERROR_WRONG_OBJECT = 'you have passed a wrong object';

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @param $id
     * @return UserInterface
     */
    public function find($id){
        $object = $this->userManager->find($id);
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
        $objects = $this->userManager->findUsers($limit, $page);
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
     * @param UserInterface|array $object
     * @return UserInterface
     */
    public function save($object){

        if(is_array($object)){
            $objectData = $object;
            $newPassword = "";
            if($object['id'] > 0){
                $object = $this->find($object['id']);
            } else {
                $object = new User();
            }
            if(isset($object['password'])){
                $newPassword = $object['password'];
            }
            $this->assignArrayToObject($object, $objectData, $this->getUserArrayFields());
        } else if(!($object instanceof User)) {
            $this->addErrorMessage(self::ERROR_WRONG_OBJECT);
        }

        if(!$this->hasError()){
            $check = $this->userManager->updateUser($object);
            if(!empty($newPassword) && $check === true){
                $check = $this->userManager->changeUserPassword($object, $newPassword);
                if($check !== true){
                    foreach($check as $pwError){
                        $this->addErrorMessage($pwError);
                    }
                }
            }
            if($check === true){
                $this->addSuccessMessage(self::SUCCESS_SAVED);
            }
        }

        return $object;
    }

    /**
     * @param int|UserInterface $object
     * @return bool
     */
    public function delete($object){
        if(is_numeric($object)){
            $object = $this->find($object);
        } else if(!($object instanceof UserInterface)) {
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
     * return a list of all field names of the Site object as Array
     * @return array
     */
    public function getUserArrayFields(){
        // only this fields are allowed
        $fields = array(
            'username',
            'email',
            'symbbType'
        );
        return $fields;
    }

    /**
     * @param UserManager $manager
     */
    public function setUserManager(UserManager $manager){
        $this->userManager = $manager;
    }

    /**
     * @param object|array $object
     * @return array
     */
    public function createArrayOfObject($object){
        $array = array();
        if(is_array($object)){
            foreach($object as $currObject){
                $array[] = $this->_createArrayOfObject($currObject);
            }
        } else {
            $array = $this->_createArrayOfObject($object);
        }
        return $array;
    }

    protected function _createArrayOfObject(UserInterface $object){
        $array = array(
            'id' =>0,
            'username' => '',
            'email' => '',
            'last_login' => '',
            'created' => ''
        );
        if(is_object($object)){
            $array['id'] = $object->getId();
            $array['username'] = $object->getUsername();
            $array['email'] = $object->getEmail();
            $array['last_login'] = $this->getISO8601ForUser($object->getLastLogin());
            $array['created'] = $this->getISO8601ForUser($object->getCreated());
        }
        return $array;
    }
}