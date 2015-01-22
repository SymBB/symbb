<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\NewsBundle\Api;

use Symbb\Core\NewsBundle\Entity\Category;
use Symbb\Core\SystemBundle\Api\AbstractCrudApi;

class SourceApi extends AbstractCrudApi
{

    /**
     * @param $object
     * @return bool
     */
    protected function isCorrectInstance($object){
        return $object instanceof Category\Source;
    }

    /**
     * @return Category
     */
    protected function createNewObject(){
        return new Category\Source();
    }


    /**
     * save a object
     * you can pass the Site object or an array with the fields
     * if you pass an array the keys must be with underscore and not with CamelCase
     * @param object|array $object
     * @return object
     */
    public function save($object)
    {

        if (is_array($object)) {
            $objectData = $object;
            if ($object['id'] > 0) {
                $object = $this->find($object['id']);
            } else {
                if($objectData["type"] == "email"){
                    $object = new Category\Source\Email();
                } else {
                    $object = new Category\Source\Feed();
                }
            }
            $this->assignArrayToObject($object, $objectData);
        } else if (!$this->isCorrectInstance($object)) {
            $this->addErrorMessage(self::ERROR_WRONG_OBJECT);
        }

        if (!$this->hasError()) {
            $check = $this->manager->save($object);
            if ($check) {
                $this->addSuccessMessage(self::SUCCESS_SAVED);
            }
        }

        return $object;
    }

    /**
     * @param $object
     * @param $direction
     * @return array|null
     */
    protected function getFieldsForObject($object, $direction)
    {

        $fields = array();
        if ($object instanceof Category\Source) {
            // only this fields are allowed
            $fields = array(
                'id',
                'name',
                'lastCall',
                'category'
            );
            if($object->getType() == "email"){
                $fields[] = "server";
                $fields[] = "username";
                $fields[] = "password";
            } else {
                $fields[] = "url";
            }
            if ($direction == "toArray") {

            }
        }

        return $fields;
    }
}