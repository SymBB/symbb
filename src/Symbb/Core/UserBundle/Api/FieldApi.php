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
use Symbb\Core\UserBundle\Entity\Field;
use Symbb\Core\UserBundle\Manager\FieldManager;

class FieldApi extends AbstractApi
{
    const ERROR_WRONG_OBJECT = 'you have passed a wrong object';

    /**
     * @var FieldManager
     */
    protected $fieldManager;

    /**
     * @param $id
     * @return Field
     */
    public function find($id){
        $object = $this->fieldManager->find($id);
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
        $objects = $this->fieldManager->findAll($limit, $page);
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
     * @param Field|array $object
     * @return Field
     */
    public function save($object){

        if(is_array($object)){
            $objectData = $object;
            if($object['id'] > 0){
                $object = $this->find($object['id']);
            } else {
                $object = new Field();
            }
            $this->assignArrayToObject($object, $objectData, $this->getFieldArrayFields());
        } else if(!($object instanceof Field)) {
            $this->addErrorMessage(self::ERROR_WRONG_OBJECT);
        }

        if(!$this->hasError()){
            $check = $this->fieldManager->update($object);
            if($check === true){
                $this->addSuccessMessage(self::SUCCESS_SAVED);
            }
        }

        return $object;
    }

    /**
     * @param int|Field $object
     * @return bool
     */
    public function delete($object){
        if(is_numeric($object)){
            $object = $this->find($object);
        } else if(!($object instanceof Field)) {
            $this->addErrorMessage(self::ERROR_WRONG_OBJECT);
        }
        if(!$this->hasError()){
            $check = $this->fieldManager->removeUser($object);
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
    public function getFieldArrayFields(){
        // only this fields are allowed
        $fields = array(
            'data_type',
            'label',
            'display_in_forum',
            'display_in_memberlist',
            'position'
        );
        return $fields;
    }

    /**
     * @param FieldManager $manager
     */
    public function setFieldManager(FieldManager $manager){
        $this->fieldManager = $manager;
    }

}