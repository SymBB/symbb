<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\ForumBundle\DependencyInjection;

use \SymBB\Core\UserBundle\Entity\UserInterface;
use \SymBB\Core\UserBundle\DependencyInjection\UserManager;
use \SymBB\Core\SystemBundle\DependencyInjection\AccessManager;

abstract class AbstractFlagHandler extends \SymBB\Core\SystemBundle\DependencyInjection\AbstractManager
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

    const LIFETIME = 86400; // 1day

    public function __construct($em, UserManager $userManager, AccessManager $accessManager, $securityContext, $memcache)
    {
        $this->em = $em;
        $this->userManager = $userManager;
        $this->accessManager = $accessManager;
        $this->securityContext = $securityContext;
        $this->memcache = $memcache;

    }

    public abstract function findOne($flag, $object, \SymBB\Core\UserBundle\Entity\UserInterface $user);

    public abstract function findFlagsByObjectAndFlag($object, $flag);

    public abstract function createNewFlag($object, \SymBB\Core\UserBundle\Entity\UserInterface $user, $flag);

    protected abstract function getMemcacheKey($flag, $object);


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
                $key = $this->getMemcacheKey($flag, $object);
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
            $user instanceof \SymBB\Core\UserBundle\Entity\UserInterface &&
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

    protected function fillMemcache($flag, $object)
    {

        $finalFlags = $this->prepareForMemcache($flag, $object);
        $key = $this->getMemcacheKey($flag, $object);
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
        $key = $this->getMemcacheKey($flag, $object);
        $users = $this->getUsersForFlag($flag, $object);
        if (!$user) {
            $user = $this->getUser();
        }
        $userId = $user->getId();
        if (isset($users[$userId])) {
            unset($users[$userId]);
            $key = $this->getMemcacheKey($flag, $object);
            $this->memcache->set($key, $users, self::LIFETIME);
        }

    }

    public function getUsersForFlag($flag, $object)
    {
        $key = $this->getMemcacheKey($flag, $object);
        $users = $this->memcache->get($key);
        if ($users === false) {
            $this->fillMemcache($flag, $object);
            $users = (array) $this->memcache->get($key);
        }
        return $users;

    }
}
