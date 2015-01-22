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

class CategoryApi extends AbstractCrudApi
{

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
                'targetForum'
            );
            if ($direction == "toArray") {
                $fields[] = 'sources';
            }
        }

        return $fields;
    }
}