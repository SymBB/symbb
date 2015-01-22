<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SystemBundle\Api;

use Doctrine\ORM\Query;
use Symbb\Core\MessageBundle\DependencyInjection\MessageManager;
use Symbb\Core\SystemBundle\Manager\AbstractManager;
use Symbb\Core\SystemBundle\Manager\AccessManager;
use Symbb\Core\UserBundle\Manager\UserManager;
use Symbb\Core\UserBundle\Entity\UserInterface;
use Symfony\Component\Translation\TranslatorInterface;
use \Doctrine\ORM\EntityManager;
use \Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

abstract class AbstractCrudApi extends AbstractApi
{

    public function find($id)
    {
        $object = $this->manager->find($id);
        if (!is_object($object)) {
            $this->addErrorMessage(self::ERROR_ENTRY_NOT_FOUND);
        }
        return $object;
    }

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
                $object = $this->createNewObject();
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
     * @param int|object $object
     * @return bool
     */
    public function delete($object)
    {
        if (is_numeric($object)) {
            $object = $this->find($object);
        } else if (!$this->isCorrectInstance($object)) {
            $this->addErrorMessage(self::ERROR_WRONG_OBJECT);
        }
        if (!$this->hasError()) {
            $check = $this->manager->remove($object);
            if ($check) {
                $this->addSuccessMessage(self::SUCCESS_DELETED);
            }
            return $check;
        }
        return false;
    }

    /**
     * @param AbstractManager $manager
     */
    public function setManager(AbstractManager $manager)
    {
        $this->manager = $manager;
    }

    protected abstract function isCorrectInstance($object);
    protected abstract function createNewObject();
}
