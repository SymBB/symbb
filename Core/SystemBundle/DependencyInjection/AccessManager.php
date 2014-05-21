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
use \Symfony\Component\Security\Core\SecurityContextInterface;
use \Symfony\Component\Security\Core\Util\ClassUtils;
use \Symfony\Component\Security\Acl\Model\SecurityIdentityInterface;
use \Symfony\Component\Security\Acl\Model\AclProviderInterface;

class AccessManager extends \SymBB\Core\SystemBundle\DependencyInjection\AbstractManager
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var array
     */
    protected $accessChecks = array();

    /**
     * @var array
     */
    protected $symbBConfig = array();

    /**
     *
     * @param type $em
     * @param \Symfony\Component\Security\Core\SecurityContextInterface $securityContext
     */
    public function __construct($em, SecurityContextInterface $securityContext, $symbbConfig)
    {
        $this->em = $em;
        $this->securityContext = $securityContext;
        $this->symbbConfig = $symbbConfig;
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
    public function grantAccess($masks, $object, $identity = null)
    {
        if ($identity === null) {
            $identity = $this->getUser();
        }

        $objectClass = ClassUtils::getRealClass($object);
        $objectId = $object->getId();

        $identityClass = ClassUtils::getRealClass($identity);
        $identityId = $identity->getId();

        foreach ((array) $masks as $mask) {
            $maskData = explode('#', $mask);
            $extension = reset($maskData);
            $access = end($maskData);
            $accessObj = new Access();
            $accessObj->setObject($objectClass);
            $accessObj->setObjectId($objectId);
            $accessObj->setIdentity($identityClass);
            $accessObj->setIdentityId($identityId);
            $accessObj->setExtension($extension);
            $accessObj->setAccess($access);
            $this->em->persist($accessObj);
        }

        $this->em->flush();
    }

    public function removeAllAccess($object, $identity)
    {

        $objectClass = ClassUtils::getRealClass($object);
        $objectId = $object->getId();

        $identityClass = ClassUtils::getRealClass($identity);
        $identityId = $identity->getId();

        $qb = $this->em->getRepository('SymBBCoreSystemBundle:Access')->createQueryBuilder('a');
        $qb->delete('a')
            ->where('object = ?object AND objectId = ?objectId AND indentity = ?identity AND indentityId = ?indentityId ')
            ->setParameter('object', $objectClass)
            ->setParameter('objectId', $objectId)
            ->setParameter('indentity', $identityClass)
            ->setParameter('indentityId', $identityId);
        $query = $qb->getQuery();
        $query->execute();

    }

    /**
     * @param array|string $masks
     * @param object $object
     * @param array|null $indentityObject
     * @param bool $checkAdditional
     * @return bool
     */
    public function addAccessCheck($masks, $object, $identity = null, $checkAdditional = true)
    {
        if ($identity === null) {
            $identity = $this->getUser();
        }

        foreach((array)$masks as $mask){
            $maskData = explode('#', $mask);
            $extension = reset($maskData);
            $access = end($maskData);

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

    public function checkAccess()
    {
        $access = $this->hasAccess();
        if (false === $access) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
        }
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
}