<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\DependencyInjection;

use Doctrine\Common\Util\ClassUtils;
use Symbb\Core\SystemBundle\Entity\Flag;
use \Symbb\Core\UserBundle\Entity\UserInterface;
use \Symbb\Core\UserBundle\Manager\UserManager;
use \Symbb\Core\SystemBundle\Manager\AccessManager;

abstract class AbstractFlagHandler extends \Symbb\Core\SystemBundle\Manager\AbstractManager
{

    /**
     * @var \Doctrine\ORM\EntityManager 
     */
    protected $em;

    /**
     * @var UserManager 
     */
    protected $userManager;


    /*
     * @var AccessManager 
     */

    protected $accessManager;

    protected $memcache;

    protected $enviroment = 'prod';

    const LIFETIME = 86400; // 1day

    public function __construct($em, UserManager $userManager, AccessManager $accessManager, $securityContext, $memcache)
    {
        $this->em = $em;
        $this->userManager = $userManager;
        $this->accessManager = $accessManager;
        $this->securityContext = $securityContext;
        $this->memcache = $memcache;
    }

    public function setEnviroment($env)
    {
        $this->enviroment = $env;
    }

    public function findOne($flag, $object, \Symbb\Core\UserBundle\Entity\UserInterface $user = null){
        if (!$user) {
            $user = $this->getUser();
        }
        $flag = $this->em->getRepository('SymbbCoreSystemBundle:Flag', 'symbb')->findOneBy(array(
            'objectClass' => ClassUtils::getRealClass(get_class($object)),
            'objectId' => $object->getId(),
            'user' => $user->getId(),
            'flag' => (string)$flag
        ));
        return $flag;
    }

    /**
     * @param $object
     * @param UserInterface $user
     * @return Flag[]
     */
    public function findAll($object, \Symbb\Core\UserBundle\Entity\UserInterface $user = null){
        if (!$user) {
            $user = $this->getUser();
        }
        $flags = $this->em->getRepository('SymbbCoreSystemBundle:Flag', 'symbb')->findBy(array(
            'objectClass' => ClassUtils::getRealClass(get_class($object)),
            'objectId' => $object->getId(),
            'user' => (string)$user->getId()
        ));
        return $flags;
    }

    public function findFlagsByObjectAndFlag($object, $flag){
        $flags = $this->em->getRepository('SymbbCoreSystemBundle:Flag', 'symbb')->findBy(array(
            'objectClass' => ClassUtils::getRealClass(get_class($object)),
            'objectId' => $object->getId(),
            'flag' => (string)$flag
        ));
        return $flags;
    }

    public function createNewFlag($object, \Symbb\Core\UserBundle\Entity\UserInterface $user, $flag){
        $flagObject = new Flag();
        $flagObject->setObject($object);
        $flagObject->setUser($user);
        $flagObject->setFlag($flag);
        return $flagObject;
    }

    public function removeFlag($object, $flag, UserInterface $user = null)
    {

        if ($user === null) {
            $user = $this->getUser();
        }

        // only if the user is a real "user" and not a guest or bot
        if ($user->getSymbbType() === 'user') {
            $flagObject = $this->findOne($flag, $object, $user);
            if (is_object($flagObject)) {
                $this->em->remove($flagObject);
                $this->em->flush();
                $this->removeFromMemchache($flag, $object, $user);
            }
        }
    }

    public function insertFlags($object, $flag = 'new')
    {

        if (is_object($this->getUser())) {
            // adding user flags
            $users = $this->userManager->findUsers();
            foreach ($users as $user) {
                if (
                    $user->getSymbbType() === 'user' &&
                    (
                    $flag !== 'new' ||
                    $user->getId() != $this->getUser()->getId() // new flag only by "other" users
                    )
                ) {
                    $this->insertFlag($object, $flag, $user, false);
                }
            }

            $this->em->flush();
        }
    }

    public function insertFlag($object, $flag, UserInterface $user = null, $flushEm = true)
    {

        if ($user === null) {
            $user = $this->getUser();
        }

        // only for real "users"
        if ($user->getSymbbType() === 'user') {

            $users = $this->getUsersForFlag($flag, $object);
            $userId = $user->getId();

            if (!isset($users[$userId])) {

                // save into database
                $flagObject = $this->createNewFlag($object, $user, $flag);
                $this->em->persist($flagObject);

                // save into memcache
                $users[$userId] = $flagObject->getCreated()->getTimestamp();
                $key = $this->_getMemcacheKey($flag, $object);
                $this->memcache->set($key, $users, self::LIFETIME);
            }
        }

        if ($flushEm) {
            $this->em->flush();
        }
    }

    public function checkFlag($object, $flag, UserInterface $user = null)
    {

        $check = false;

        if (!$user) {
            $user = $this->getUser();
        }

        if (
            $user instanceof \Symbb\Core\UserBundle\Entity\UserInterface &&
            $user->getSymbbType() === 'user'
        ) {
            $users = $this->getUsersForFlag($flag, $object);
            foreach ($users as $userId => $timestamp) {
                if (
                    $userId == $user->getId()
                ) {
                    $check = true;
                    break;
                }
            }
        }

        return $check;
    }

    protected function getMemcacheKey($flag, $object){
        $key = ClassUtils::getRealClass(get_class($object)).'_'.$object->getId().'_'.$flag;
        return $key;
    }

    private function _getMemcacheKey($flag, $object)
    {
        $key = $this->getMemcacheKey($flag, $object);
        $key = $key . "_symbb_" . $this->enviroment;
        
        return $key;
    }

    protected function fillMemcache($flag, $object)
    {
        $finalFlags = $this->prepareForMemcache($flag, $object);
        $key = $this->_getMemcacheKey($flag, $object);
        $this->memcache->set($key, $finalFlags, self::LIFETIME);
    }

    protected function prepareForMemcache($flag, $object)
    {
        $flags = $this->findFlagsByObjectAndFlag($object, $flag);

        $finalFlags = array();
        foreach ($flags as $flagObject) {
            $userId = $flagObject->getUser()->getId();
            $finalFlags[$userId] = $userId;
        }
        return $finalFlags;
    }

    protected function removeFromMemchache($flag, $object, UserInterface $user)
    {
        $key = $this->_getMemcacheKey($flag, $object);
        $users = $this->getUsersForFlag($flag, $object);
        if (!$user) {
            $user = $this->getUser();
        }
        $userId = $user->getId();
        if (isset($users[$userId])) {
            unset($users[$userId]);
            $key = $this->_getMemcacheKey($flag, $object);
            $this->memcache->set($key, $users, self::LIFETIME);
        }
    }

    public function getUsersForFlag($flag, $object)
    {
        $key = $this->_getMemcacheKey($flag, $object);
        $users = $this->memcache->get($key);
        if (!is_array($users)) {
            $this->fillMemcache($flag, $object);
            $users = (array) $this->memcache->get($key);
        }
        return $users;
    }
}
