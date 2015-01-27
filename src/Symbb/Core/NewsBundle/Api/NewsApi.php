<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\NewsBundle\Api;

use Symbb\Core\NewsBundle\Entity\Category\Entry;
use Symbb\Core\NewsBundle\Entity\Category\Source\Email;
use Symbb\Core\NewsBundle\Entity\Category\Source\Feed;
use Symbb\Core\SystemBundle\Api\AbstractApi;

class NewsApi extends AbstractApi
{

    /**
     * return a array with all Sites
     * @return array
     */
    public function getList()
    {
        $objects = $this->manager->findAll();
        $this->addPaginationData($objects);
        $objects = $objects->getItems();
        if (empty($objects)) {
            $this->addInfoMessage(self::INFO_NO_ENTRIES_FOUND);
        }
        return $objects;
    }

    public function collectNews(){
        $objects = $this->manager->collectNews();
        if (empty($objects)) {
            $this->addInfoMessage(self::INFO_NO_ENTRIES_FOUND);
        }
        return $objects;
    }

    /**
     * @param $object
     * @param $direction
     * @return array|null
     */
    protected function getFieldsForObject($object, $direction)
    {

        $fields = array();
        if ($object instanceof Entry) {
            // only this fields are allowed
            $fields = array(
                'id',
                'title',
                'created',
            );
        }

        return $fields;
    }

    public function createArrayOfObject(Entry $object){
        $data = array();
        $data["id"] = $object->getId();
        $data["title"] = $object->getTitle();
        $data["created"] = $this->getISO8601ForUser($object->getCreated());
        $source = $object->getSource();
        if($source instanceof Email){
            $data["type"] = "email";
        } else if($source instanceof Feed){
            $data["type"] = "feed";
        }
        $data["category"] = array(
            "id" => $object->getCategory()->getId(),
            "name" => $object->getCategory()->getName(),
        );
        return $data;
    }

}