<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SystemBundle\Manager;

use Doctrine\Common\Util\ClassUtils;
use Symbb\Core\SystemBundle\Entity\Flag;
use \Symbb\Core\UserBundle\Entity\UserInterface;
use \Symbb\Core\UserBundle\Manager\UserManager;
use \Symbb\Core\SystemBundle\Manager\AccessManager;

/**
 * Class AbstractFlagHandler
 * @package Symbb\Core\ForumBundle\DependencyInjection
 */
abstract class AbstractFlagHandler extends \Symbb\Core\SystemBundle\Manager\AbstractManager
{
    const FLAG_NEW = "new";
    const FLAG_NOTIFY = "notify";
    const FLAG_ANSWERED = "answered";

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

    /**
     * @var AccessManager
     */
    protected $accessManager;

    /**
     * @var
     */
    protected $memcache;

    /**
     * @var string
     */
    protected $enviroment = 'prod';

    /**
     *
     */
    const LIFETIME = 86400; // 1day

    /**
     * @param $em
     * @param UserManager $userManager
     * @param AccessManager $accessManager
     * @param $securityContext
     * @param $memcache
     */
    public function __construct($em, UserManager $userManager, AccessManager $accessManager, $securityContext, $memcache)
    {
        $this->em = $em;
        $this->userManager = $userManager;
        $this->accessManager = $accessManager;
        $this->securityContext = $securityContext;
        $this->memcache = $memcache;
    }

    /**
     * @param $env
     */
    public function setEnviroment($env)
    {
        $this->enviroment = $env;
    }

    /**
     * @param $flag
     * @param $object
     * @param UserInterface $user
     * @return mixed
     */
    public function findOne($flag, $object, \Symbb\Core\UserBundle\Entity\UserInterface $user = null)
    {
        if (!$user) {
            $user = $this->getUser();
        }
        $flag = $this->em->getRepository('SymbbCoreSystemBundle:Flag', 'symbb')->findOneBy(array(
            'objectClass' => ClassUtils::getRealClass(get_class($object)),
            'objectId' => $object->getId(),
            'user' => $user->getId(),
            'flag' => (string)$flag
        ));
        $flags = array($flag);
        //static stuff who is not assigned to an user
        $flags = $this->addStaticFlags($object, $flags, $flag);
        return reset($flags);
    }

    /**
     * @param $object
     * @param $flags
     * @return mixed
     */
    protected function addStaticFlags($object, $flags, $searchFlag = null){
        return $flags;
    }

    /**
     * @param $object
     * @param UserInterface $user
     * @return Flag[]
     */
    public function findAll($object, UserInterface $user = null)
    {
        if (!$user) {
            $user = $this->getUser();
        }
        $flags = $this->em->getRepository('SymbbCoreSystemBundle:Flag', 'symbb')->findBy(array(
            'objectClass' => ClassUtils::getRealClass(get_class($object)),
            'objectId' => $object->getId(),
            'user' => (string)$user->getId()
        ));
        //static stuff who is not assigned to an user
        $flags = $this->addStaticFlags($object, $flags);
        return $flags;
    }

    /**
     * @param $object
     * @param $flag
     * @return Flag[]
     */
    public function findFlagsByObjectAndFlag($object, $flag)
    {
        $flags = $this->em->getRepository('SymbbCoreSystemBundle:Flag', 'symbb')->findBy(array(
            'objectClass' => ClassUtils::getRealClass(get_class($object)),
            'objectId' => $object->getId(),
            'flag' => (string)$flag
        ));
        //static stuff who is not assigned to an user
        $flags = $this->addStaticFlags($object, $flags, $flag);
        return $flags;
    }

    /**
     * @param $object
     * @param $flag
     * @param UserInterface $user
     * @return Flag[]
     */
    public function findFlagsByClassAndFlag($object, $flag, UserInterface $user = null)
    {
        if (!$user) {
            $user = $this->getUser();
        }
        $flags = $this->em->getRepository('SymbbCoreSystemBundle:Flag', 'symbb')->findBy(array(
            'objectClass' => ClassUtils::getRealClass(get_class($object)),
            'flag' => (string)$flag,
            'user' => (string)$user->getId()
        ));
        //static stuff who is not assigned to an user
        $flags = $this->addStaticFlags($object, $flags, $flag);
        return $flags;
    }

    /**
     * @param $object
     * @param UserInterface $user
     * @param $flag
     * @return Flag
     */
    public function createNewFlag($object, \Symbb\Core\UserBundle\Entity\UserInterface $user, $flag)
    {
        $flagObject = new Flag();
        $flagObject->setObject($object);
        $flagObject->setUser($user);
        $flagObject->setFlag($flag);
        return $flagObject;
    }

    /**
     * @param $object
     * @param $flag
     * @param UserInterface $user
     */
    public function removeFlag($object, $flag, UserInterface $user = null)
    {

        if ($user === null) {
            $user = $this->getUser();
        }

        // only if the user is a real "user" and not a guest or bot
        if ($user->getSymbbType() === 'user') {
            $flagObject = $this->findOne($flag, $object, $user);
            if (is_object($flagObject) && $flagObject->getId() > 0) {
                $this->em->remove($flagObject);
                $this->em->flush();
            }
        }
    }

    /**
     * @param $object
     * @param string $flag
     */
    public function insertFlags($object, $flag = null)
    {
        if(!$flag){
            $flag = AbstractFlagHandler::FLAG_NEW;
        }
        if (is_object($this->getUser())) {
            // adding user flags
            $users = $this->userManager->findUsers();
            foreach ($users as $user) {
                if (
                    $user->getSymbbType() === 'user' &&
                    (
                        $flag !== AbstractFlagHandler::FLAG_NEW ||
                        $user->getId() != $this->getUser()->getId() // new flag only by "other" users
                    )
                ) {
                    $this->insertFlag($object, $flag, $user, false);
                }
            }

            $this->em->flush();
        }
    }

    /**
     * @param $object
     * @param $flag
     * @param UserInterface $user
     * @param bool $flushEm
     */
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
            }
        }

        if ($flushEm) {
            $this->em->flush();
        }
    }

    /**
     * @param $object
     * @param $flag
     * @param UserInterface $user
     * @return bool
     */
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
            foreach ($users as $userId => $flagKey) {
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

    /**
     * @param $flag
     * @param $object
     * @return array
     */
    public function getUsersForFlag($flag, $object)
    {
        $flags = $this->findFlagsByObjectAndFlag($object, $flag);
        $finalFlags = array();
        foreach ($flags as $flagObject) {
            $userId = $flagObject->getUser()->getId();
            $finalFlags[$userId] = $flagObject->getFlag();
        }
        return $finalFlags;
    }
}
