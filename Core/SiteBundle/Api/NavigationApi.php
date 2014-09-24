<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SiteBundle\Api;

use SymBB\Core\SiteBundle\Entity\Navigation;
use SymBB\Core\SiteBundle\Manager\NavigationManager;
use SymBB\Core\SiteBundle\Manager\SiteManager;
use SymBB\Core\SystemBundle\Api\AbstractApi;

class NavigationApi extends AbstractApi
{
    const ERROR_WRONG_OBJECT = 'you have passed a wrong object';

    /**
     * @var NavigationManager
     */
    protected $navigationManager;

    /**
     * @var SiteManager
     */
    protected $siteManager;

    /**
     * @param $id
     * @return Navigation
     */
    public function find($id){
        $object = $this->navigationManager->find($id);
        if(!is_object($object)){
            $this->addErrorMessage(self::ERROR_ENTRY_NOT_FOUND);
        }
        return $object;
    }

    /**
     * return a array with all Navigations
     * @return array
     */
    public function getList(){
        $objects = $this->navigationManager->findAll();
        $objects = $objects->getItems();
        if(empty($objects)){
            $this->addInfoMessage(self::INFO_NO_ENTRIES_FOUND);
        }
        return $objects;
    }

    /**
     * save a Navigation
     * you can pass the Navigation object or an array with the fields
     * if you pass an array the keys must be with underscore and not with CamelCase
     * @param Navigation|array $object
     * @return Navigation
     */
    public function save($object){

        if(is_array($object)){
            $objectData = $object;
            if($object['id'] > 0){
                $object = $this->find($object['id']);
            } else {
                $object = new Navigation();
                $site = $this->siteManager->find($objectData['site']);
                $object->setSite($site);
            }
            if(isset($objectData['site'])){
                unset($objectData['site']);
            }
            $this->assignArrayToObject($object, $objectData, $this->getNavigationArrayFields());
        } else if(!($object instanceof Navigation)) {
            $this->addErrorMessage(self::ERROR_WRONG_OBJECT);
        }

        if(!$this->hasError()){
            $check = $this->navigationManager->save($object);
            if($check){
                $this->addSuccessMessage(self::SUCCESS_SAVED);
                return $object;
            }
        }
        return null;
    }

    /**
     * @param int|Navigation $object
     * @return bool
     */
    public function delete($object){
        if(is_numeric($object)){
            $object = $this->find($object);
        } else if(!($object instanceof Navigation)) {
            $this->addErrorMessage(self::ERROR_WRONG_OBJECT);
        }
        if(!$this->hasError()){
            $check = $this->navigationManager->remove($object);
            if($check){
                $this->addSuccessMessage(self::SUCCESS_DELETED);
            }
            return $check;
        }
        return false;
    }


    /**
     * @param $id
     * @return Navigation\Item
     */
    public function findItem($id){
        $object = $this->navigationManager->findItem($id);
        if(!is_object($object)){
            $this->addErrorMessage(self::ERROR_ENTRY_NOT_FOUND);
        }
        return $object;
    }

    /**
     * @param array|Navigation\Item $item
     */
    public function saveItem($item){
        $object = null;
        if(is_array($item)){
            $itemData = $item;

            if($item['id'] > 0){
                $object = $this->findItem($item['id']);
            } else {
                $object = new Navigation\Item();
            }

            if(isset($itemData['parentItemId']) && $itemData['parentItemId'] > 0){
                $parentItem = $this->findItem($itemData['parentItemId']);
                $object->setParentItem($parentItem);
            }
            if(isset($itemData['parentItemId'])){
                unset($itemData['parentItemId']);
            }

            if(isset($itemData['navigationId']) && $itemData['navigationId'] > 0){
                $parentItem = $this->find($itemData['navigationId']);
                $object->setNavigation($parentItem);
            }
            if(isset($itemData['navigationId'])){
                unset($itemData['navigationId']);
            }

            $this->assignArrayToObject($object, $itemData, $this->getNavigationItemArrayFields());

        } else if(!($object instanceof Navigation\Item)) {
            $this->addErrorMessage(self::ERROR_WRONG_OBJECT);
        }

        if(!$this->hasError()){
            $check = $this->navigationManager->saveItem($object);
            if($check){
                $this->addSuccessMessage(self::SUCCESS_SAVED);
            }
        }
        return $object;
    }

    /**
     * @param int|Navigation\Item $object
     * @return bool
     */
    public function deleteItem($object){
        if(is_numeric($object)){
            $object = $this->findItem($object);
        } else if(!($object instanceof Navigation\Item)) {
            $this->addErrorMessage(self::ERROR_WRONG_OBJECT);
        }
        if(!$this->hasError()){
            $check = $this->navigationManager->removeItem($object);
            if($check){
                $this->addSuccessMessage(self::SUCCESS_DELETED);
            }
            return $check;
        }
        return false;
    }

    /**
     * return a list of all field names of the  object as Array
     * @return array
     */
    public function getNavigationItemArrayFields(){
        // only this fields are allowed
        $fields = array(
            'title',
            'type',
            'symfony_route',
            'symfony_route_params',
            'fix_url',
            'position'
        );
        return $fields;
    }

    /**
     * return a list of all field names of the  object as Array
     * @return array
     */
    public function getNavigationArrayFields(){
        // only this fields are allowed
        $fields = array(
            'title',
            'nav_key'
        );
        return $fields;
    }

    /**
     * @param NavigationManager $manager
     */
    public function setNavigationManager(NavigationManager $manager){
        $this->navigationManager = $manager;
    }

    /**
     * @param SiteManager $siteManager
     */
    public function setSiteManager(SiteManager $siteManager){
        $this->siteManager = $siteManager;
    }
}