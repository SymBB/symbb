<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SystemBundle\Manager;

use Symbb\Core\SystemBundle\Entity\Access;
use Symbb\Core\SystemBundle\Security\Authorization\AbstractVoter;
use Symbb\Core\UserBundle\Entity\UserInterface;
use \Symfony\Component\Security\Core\Util\ClassUtils;

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

    /**
     * @var AbstractVoter[]
     */
    protected $voterList = array();

    protected $memcache;

    protected $accessCache = array();

    const CACHE_LIFETIME = 86400; // 1day

    public function __construct($symbbConfig, $container)
    {
        $this->container = $container;
        $this->symbbConfig = $symbbConfig;
        $this->em =  $this->container->get('doctrine.orm.symbb_entity_manager');
        $this->memcache = $container->get('memcache.acl');
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
    public function grantAccess($access, $object, $identity = null)
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
        $accessObj->setAccess($access);
        $this->em->persist($accessObj);

        $this->em->flush();

        $this->memcache->delete('symbb_acl_cache');
    }

    public function removeAllAccess($object, $identity)
    {

        $objectClass = ClassUtils::getRealClass($object);
        $objectId = $object->getId();

        $identityClass = ClassUtils::getRealClass($identity);
        $identityId = $identity->getId();

        $qb = $this->em->getRepository('SymbbCoreSystemBundle:Access')->createQueryBuilder('a');
        $qb->delete('SymbbCoreSystemBundle:Access a')
            ->where('a.object = :object AND a.objectId = :objectId AND a.identity = :identity AND a.identityId = :identityId ')
            ->setParameter('object', $objectClass)
            ->setParameter('objectId', $objectId)
            ->setParameter('identity', $identityClass)
            ->setParameter('identityId', $identityId);
        $query = $qb->getQuery();

        $query->execute();

        $this->memcache->delete('symbb_acl_cache');

    }

    /**
     * @param array|string $masks
     * @param object $object
     * @param array|null $indentityObject
     * @return bool
     */
    public function addAccessCheck($access, $object, $identity = null)
    {
        if ($identity === null) {
            $identity = $this->getUser();
        }

        $objectClass = ClassUtils::getRealClass($object);
        $objectId = $object->getId();

        $identityList = array($identity);

        if($identity instanceof UserInterface){
            $groups = $identity->getGroups();
            $identityList = array_merge($identityList, $groups->toArray());
        }

        foreach($identityList as $currIdentity){

            $identityClass = ClassUtils::getRealClass($currIdentity);
            $identityId = $currIdentity->getId();

            $this->accessChecks[] = array(
                'object' => $objectClass,
                'objectId' => $objectId,
                'identity' => $identityClass,
                'identityId' => $identityId,
                'access' => $access
            );
        }


    }

    /**
     * @return bool
     */
    public function hasAccess()
    {
        $access = false;
        foreach ($this->accessChecks as $data) {
            $accessList = $this->checkCache($data['object'], $data['objectId'], $data['identity'], $data['identityId']);
            if (in_array(strtolower($data['access']), $accessList)) {
                $access = true;
                break;
            }
        }
        $this->accessChecks = array();
        return $access;
    }

    /**
     * @param $objectClass
     * @param $objectId
     * @param $identityClass
     * @param $identityId
     * @return array
     */
    public function checkCache($objectClass, $objectId, $identityClass, $identityId){

        $key    = $this->generateCacheKey($objectClass, $objectId, $identityClass, $identityId);
        $cache  = $this->accessCache;

        if(empty($cache)){

            $cache = $this->memcache->get('symbb_acl_cache');

            if(!$cache){
                $accessList = $this->em->getRepository('SymbbCoreSystemBundle:Access')->findAll();
                $cache = array();
                foreach($accessList as $access){
                    $currKey = $this->generateCacheKey($access->getObject(), $access->getObjectId(), $access->getIdentity(), $access->getIdentityId());
                    $accessKey = $access->getAccess();
                    $cache[$currKey][$accessKey] = $accessKey;
                }
                $this->memcache->set('symbb_acl_cache', $cache, self::CACHE_LIFETIME);
            }

            $this->accessCache = $cache;
        }

        $accessData = array();

        if(isset($cache[$key])){
            $accessData =  $cache[$key];
        }

        if(!$accessData){
            $accessData = array();
        }

        return $accessData;
    }

    /**
     * @param $objectClass
     * @param $objectId
     * @param $identityClass
     * @param $identityId
     * @return string
     */
    protected function generateCacheKey($objectClass, $objectId, $identityClass, $identityId){
        $key = 'acl_'.($objectClass.$objectId.$identityClass.$identityId);
        return $key;
    }
    /**
     *
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->container->get('security.context')->getToken()->getUser();
    }

    /**
     * @param $objectFrom
     * @param $objectTo
     * @param $identity
     */
    public function copyAccessForIdentity($objectFrom, $objectTo, $identity){

        $objectClass = ClassUtils::getRealClass($objectFrom);
        $objectId = $objectFrom->getId();

        $identityClass = ClassUtils::getRealClass($identity);
        $identityId = $identity->getId();

        $criteria = array(
            'object' => $objectClass,
            'objectId' => $objectId,
            'identity' => $identityClass,
            'identityId' => $identityId
        );

        $accessList = $this->em->getRepository('SymbbCoreSystemBundle:Access')->findBy($criteria);

        $this->removeAllAccess($objectTo, $identity);
        foreach($accessList as $access){
            $this->grantAccess($access->getAccess(), $objectTo, $identity);
        }
    }

    /**
     * @param $object
     * @param $identity
     * @param $set
     */
    public function applyAccessSetForIdentity($object, $identity, $set){
        $sets = $this->container->get('symbb.core.access.voter.manager')->getAccessSetList($object);
        if(isset($sets[$set])){
            $accessList = $sets[$set];
            $this->removeAllAccess($object, $identity);
            foreach($accessList as $access){
                $this->grantAccess($access, $object, $identity);
            }
        }
    }
}