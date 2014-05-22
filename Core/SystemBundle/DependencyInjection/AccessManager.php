<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SystemBundle\DependencyInjection;

use SymBB\Core\SystemBundle\Entity\Access;
use SymBB\Core\UserBundle\Entity\UserInterface;
use \Symfony\Component\Security\Core\SecurityContextInterface;
use \Symfony\Component\Security\Core\Util\ClassUtils;
use \Symfony\Component\Security\Acl\Model\SecurityIdentityInterface;
use \Symfony\Component\Security\Acl\Model\AclProviderInterface;

class AccessManager
{

    /**
     * @var array
     */
    protected $accessChecks = array();

    /**
     * @var array
     */
    protected $symbbConfig = array();

    protected $container;

    public function __construct($symbbConfig, $container)
    {
        $this->container = $container;
        $this->symbbConfig = $symbbConfig;
        $this->em =  $this->container->get('doctrine.orm.symbb_entity_manager');
    }

    /**
     * check if the user are logged in or not
     * @return boolean
     */
    public function isAnonymous()
    {
        if (!is_object($this->getUser())) {
            return true;
        }
        return false;
    }

    /**
     *
     * @param array $mask
     * @param object $object
     * @param object $identity
     * @throws Exception
     */
    public function grantAccess($extension, $access, $object, $identity = null)
    {
        if ($identity === null) {
            $identity = $this->getUser();
        }

        $objectClass = ClassUtils::getRealClass($object);
        $objectId = $object->getId();

        $identityClass = ClassUtils::getRealClass($identity);
        $identityId = $identity->getId();

        $accessObj = new Access();
        $accessObj->setObject($objectClass);
        $accessObj->setObjectId($objectId);
        $accessObj->setIdentity($identityClass);
        $accessObj->setIdentityId($identityId);
        $accessObj->setExtension($extension);
        $accessObj->setAccess($access);
        $this->em->persist($accessObj);

        $this->em->flush();
    }

    public function removeAllAccess($object, $identity)
    {

        $objectClass = ClassUtils::getRealClass($object);
        $objectId = $object->getId();

        $identityClass = ClassUtils::getRealClass($identity);
        $identityId = $identity->getId();

        $qb = $this->em->getRepository('SymBBCoreSystemBundle:Access')->createQueryBuilder('a');
        $qb->delete('SymBBCoreSystemBundle:Access a')
            ->where('a.object = :object AND a.objectId = :objectId AND a.identity = :identity AND a.identityId = :identityId ')
            ->setParameter('object', $objectClass)
            ->setParameter('objectId', $objectId)
            ->setParameter('identity', $identityClass)
            ->setParameter('identityId', $identityId);
        $query = $qb->getQuery();
        $query->execute();

    }

    /**
     * @param array|string $masks
     * @param object $object
     * @param array|null $indentityObject
     * @return bool
     */
    public function addAccessCheck($extension, $access, $object, $identity = null)
    {
        if ($identity === null) {
            $identity = $this->getUser();
        }

        $objectClass = ClassUtils::getRealClass($object);
        $objectId = $object->getId();

        $identityClass = ClassUtils::getRealClass($identity);
        $identityId = $identity->getId();

        $this->accessChecks[] = array(
            'object' => $objectClass,
            'objectId' => $objectId,
            'identity' => $identityClass,
            'identityId' => $identityId,
            'extension' => $extension,
            'access' => $access
        );
    }

    public function hasAccess()
    {
        $access = false;
        foreach ($this->accessChecks as $data) {

            $accessObj = $this->em->getRepository('SymBBCoreSystemBundle:Access')->findOneBy($data);
            if (is_object($accessObj)) {
                $access = true;
                break;
            }
        }
        $this->accessChecks = array();
        return $access;
    }

    public function getAccessList($object){
        $list = array();
        $objectClass = ClassUtils::getRealClass($object);
        if(isset($this->symbbConfig['access'][$objectClass])){
            $list = $this->symbbConfig['access'][$objectClass];
        }
        return $list;
    }

    /**
     *
     * @return UserInterface
     */
    public function getUser()
    {
        if (!is_object($this->user)) {
            $this->user = $this->container->get('security.context')->getToken()->getUser();
        }
        return $this->user;

    }
}