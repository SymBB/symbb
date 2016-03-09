<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\UserBundle\Manager;

use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\Query;
use Symbb\Core\UserBundle\Entity\User;
use Symbb\Core\UserBundle\Entity\User\Data;
use \Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use \Doctrine\ORM\EntityManager;
use \Symbb\Core\UserBundle\Entity\UserInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator;

/**
 *
 * DONT EXTEND FROM Abstract Manager, Abstract manager is injecting this Manager
 *
 * Class UserManager
 * @package Symbb\Core\UserBundle\DependencyInjection
 */
class UserManager
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var SecurityFactoryInterface
     */
    protected $securityFactory;

    /**
     * @var string
     */
    protected $userClass = '';

    protected $paginator;


    protected $securityContext;

    protected $securityContextToken;

    /**
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $dispatcher;

    protected $container;

    protected $postCountCache = array();

    protected $symbbDataCache = array();

    public function __construct($container)
    {
        $config = $container->getParameter('symbb_config');
        $this->emUser = $container->get('doctrine.orm.'.$config['usermanager']['entity_manager'].'_entity_manager');
        $this->em = $container->get('doctrine.orm.symbb_entity_manager');
        $this->securityFactory = $container->get('security.encoder_factory');
        $this->config = $config['usermanager'];
        $this->userClass = $this->config['user_class'];
        $this->paginator = $container->get('knp_paginator');
        $this->securityContextToken = $container->get('security.token_storage');
        $this->securityContext = $container->get('security.authorization_checker');
        $this->dispatcher = $container->get('event_dispatcher');
        $this->container = $container;
        $this->translator = $container->get("translator");
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function getRequest()
    {
        return $this->container->get('request');
    }

    /**
     *
     * @return \Symbb\Core\UserBundle\Entity\UserInterface
     */
    public function getCurrentUser()
    {
        return $this->securityContextToken->getToken()->getUser();
    }


    /**
     * update the given user
     * @param \Symbb\Core\UserBundle\Entity\UserInterface $user
     * @return bool
     */
    public function updateUser(UserInterface $user)
    {
        $user->setChangedValue();
        $this->emUser->persist($user);
        $this->emUser->flush();
        return true;
    }

    /**
     * update the given user data
     * @param \Symbb\Core\UserBundle\Entity\User\Data $user
     */
    public function updateUserData(Data $data, $flush = true)
    {
        $this->em->persist($data);
        if($flush){
            $this->em->flush();
        }

        //return array with sf validator errors
        return new \Symfony\Component\Validator\ConstraintViolationList();
    }

    /**
     * @param UserInterface $user
     * @return bool
     */
    public function removeUser(UserInterface $user)
    {
        $this->emUser->remove($user);
        $this->emUser->flush();
        return true;
    }

    public function remove(UserInterface $user){
        return $this->removeUser($user);
    }

    /**
     * @param UserInterface $user
     * @param $newPassword
     * @return ConstraintViolationList
     */
    public function changeUserPassword(UserInterface $user, $newPassword)
    {
        $user->setChangedValue();
        $encoder = $this->securityFactory->getEncoder($user);
        $password = $encoder->encodePassword($newPassword, $user->getSalt());
        $user->setPassword($password);

        /**
         * @var $validator Validator
         */
        $validator = $this->container->get('validator');
        $passwordConstraints = $this->getPasswordValidatorConstraints();
        $passwordConstraints[] = new \Symfony\Component\Validator\Constraints\NotBlank();
        $errorsPassword = $validator->validateValue($newPassword, $passwordConstraints);

        if ($errorsPassword->count() === 0) {
            $this->emUser->persist($user);
            $this->emUser->flush();
        }

        return $errorsPassword;
    }

    /**
     * create a new User
     * @return UserInterface
     */
    public function createUser($username = "")
    {
        $userClass = $this->userClass;
        $user = new $userClass();
        if(!empty($username)){
            $user->setUsername($username);
        }
        return $user;
    }

    /**
     *
     * @param type $userId
     * @return \Symbb\Core\UserBundle\Entity\UserInterface
     */
    public function find($userId)
    {
        $user = $this->emUser->getRepository($this->userClass)->find($userId);
        return $user;
    }

    /**
     *
     * @param string $username
     * @return \Symbb\Core\UserBundle\Entity\UserInterface
     */
    public function findByUsername($username)
    {
        $user = $this->emUser->getRepository($this->userClass)->findOneBy(array('username' => $username));
        return $user;
    }

    /**
     *
     * @return UserInterface[]
     */
    public function findUsers($limit = 20, $page = 1)
    {
        $users = $this->findBy(array("symbbType" => "user"), $limit, $page);
        return $users;
    }

    public function countUsers()
    {
        $users = $this->findUsers(0);
        return count($users);
    }

    public function getClass()
    {
        return $this->userClass;
    }

    public function findBy($criteria, $limit, $page = 1)
    {

        $qb = $this->emUser->getRepository($this->userClass)->createQueryBuilder('u');
        $qb->select("u");
        $whereParts = array();
        $i = 0;
        foreach ($criteria as $field => $value) {
            $valueKey = uniqid('value_');
            if (\is_array($value)) {
                $whereParts[] = "u." . $field . " " . reset($value) . " ?" . $i . "";
                $value = end($value);
            } else {
                $whereParts[] = "u." . $field . " = ?" . $i . "";
            }
            $qb->setParameter($i, $value);
            $i++;
        }
        if (!empty($whereParts)) {
            $qb->add("where", implode(' AND ', $whereParts));
        }
        $qb->orderBy("u.username", "ASC");

        $query = $qb->getQuery();
        $pagination = $this->createPagination($query, $page, $limit);
        return $pagination;
    }

    public function getTimezone()
    {
        $user = $this->getCurrentUser();
        $data = $this->getSymbbData($user);
        $tz = $data->getTimezone();

        if (!empty($tz)) {
            $tz = new \DateTimeZone($tz);
        } else {
            $now = new \DateTime;
            $tz = $now->getTimezone();
        }

        return $tz;
    }

    public function getSignature(\Symbb\Core\UserBundle\Entity\UserInterface $user = null)
    {
        if (!$user) {
            $user = $this->getCurrentUser();
        }

        $data = $this->getSymbbData($user);
        $signature = $data->getSignature();

        $event = new \Symbb\Core\UserBundle\Event\UserParseSignatureEvent($user, $signature);
        $this->dispatcher->dispatch("symbb.core.user.parse.signature", $event);

        $signature = $event->getSignature();
        return $signature;
    }

    public function getAbsoluteAvatarUrl(\Symbb\Core\UserBundle\Entity\UserInterface $user = null)
    {
        $url = $this->getAvatar($user);
        $host = '';

        if (strpos($url, 'http') === false) {
            $host = "http://" . $this->getRequest()->server->get('HTTP_HOST');
        }

        return $host . $url;
    }

    public function getAvatar(\Symbb\Core\UserBundle\Entity\UserInterface $user = null)
    {
        if (!$user) {
            $user = $this->getCurrentUser();
        }

        if(!is_object($user)){
            $user = $this->find($user);
        }

        $data = $this->getSymbbData($user);
        $avatar = $data->getAvatar();
        if (empty($avatar)) {
            $avatar = '/bundles/symbbtemplatedefault/images/avatar/empty.gif';
        }
        return $avatar;
    }

    public function getPostCount($user = null)
    {
        if (!$user) {
            $user = $this->getCurrentUser();
        }

        if(is_object($user)){
            $user = $user->getId();
        }

        if (!isset($this->postCountCache[$user])) {
            $qb = $this->em->getRepository('SymbbCoreForumBundle:Post', 'symbb')->createQueryBuilder('p');
            $qb->select('COUNT(p.id)');
            $qb->where("p.authorId = " . $user);
            $count = $qb->getQuery()->getSingleScalarResult();
            $this->postCountCache[$user] = $count;
        } else {
            $count = $this->postCountCache[$user];
        }

        return $count;
    }

    public function getLastPosts($user = null, $limit = 20)
    {
        if (!$user) {
            $user = $this->getCurrentUser();
        }

        if(is_object($user)){
            $user = $user->getId();
        }

        $qb = $this->em->getRepository('SymbbCoreForumBundle:Post', 'symbb')->createQueryBuilder('p');
        $qb->select('p');
        $qb->where("p.authorId = " . $user);
        $qb->orderBy("p.created", "desc");
        $dql = $qb->getDql();
        $query = $this->em->createQuery($dql);
        $posts = $this->createPagination($query, 1, $limit);
        return $posts;
    }

    public function getTopicCount($user = null)
    {
        if (!$user) {
            $user = $this->getCurrentUser();
        }

        if(is_object($user)){
            $user = $user->getId();
        }

        $qb = $this->em->getRepository('SymbbCoreForumBundle:Topic', 'symbb')->createQueryBuilder('p');
        $qb->select('COUNT(p.id)');
        $qb->where("p.authorId = " . $user);
        $count = $qb->getQuery()->getSingleScalarResult();
        return $count;
    }

    /**
     * manually login e.g for Tapatalk Extension
     * mtehod will be generate a cookie
     * @param type $username
     * @param type $password
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $providerKey
     * @param \Symfony\Component\HttpFoundation\Response $redirectResponse
     * @return boolean
     */
    public function login($username, $password, \Symfony\Component\HttpFoundation\Request $request, $providerKey, \Symfony\Component\HttpFoundation\Response $redirectResponse)
    {

        $user = $this->findByUsername($username);

        $encoder = $this->securityFactory->getEncoder($user);
        $password2 = $encoder->encodePassword($password, $user->getSalt());
        if ($user->getPassword() === $password2) {
            $token = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken($user, $user->getPassword(), 'symbb', $user->getRoles());

            $this->securityContextToken->setToken($token);

            $securityKey = 'myKey';
            $random = new \Symfony\Component\Security\Core\Util\SecureRandom();

            $persistenService = new \Symfony\Component\Security\Http\RememberMe\PersistentTokenBasedRememberMeServices(array($this), $providerKey, $securityKey, array('path' => '/', 'name' => 'REMEMBERME', 'domain' => null, 'secure' => false, 'httponly' => true, 'lifetime' => 31536000, 'always_remember_me' => true, 'remember_me_parameter' => '_remember_me'), null, $random);
            $persistenService->setTokenProvider(new \Symfony\Component\Security\Core\Authentication\RememberMe\InMemoryTokenProvider());

            $persistenService->loginSuccess($request, $redirectResponse, $token);

            return true;
        }

        return false;
    }

    public function getPasswordValidatorConstraints()
    {
        $constraints = array();

        $strength = 3;

        if ($strength >= 1) {
            // Uppercase
            $constraints[] = new \Symfony\Component\Validator\Constraints\Regex(array(
                "pattern" => "/[A-Z]/",
                'message' => $this->translator->trans('Your Password need a minimum of 1 uppercase character', array(), 'validators')
            ));
            $constraints[] = new \Symfony\Component\Validator\Constraints\Length(array(
                "min" => 6,
                'minMessage' => $this->translator->trans('Your Password need a minimum of 6 characters', array(), 'validators')
            ));
        }

        if ($strength >= 2) {
            //lowercase
            $constraints[] = new \Symfony\Component\Validator\Constraints\Regex(array(
                "pattern" => "/[a-z]/",
                'message' => $this->translator->trans('Your Password need a minimum of 1 lowercase character', array(), 'validators')
            ));
        }

        if ($strength >= 3) {
            //lowercase
            $constraints[] = new \Symfony\Component\Validator\Constraints\Regex(array(
                "pattern" => "/[0-9]/",
                'message' => $this->translator->trans('Your Password need a minimum of 1 number', array(), 'validators')
            ));
        }

        if ($strength >= 4) {
            //none word characters
            $constraints[] = new \Symfony\Component\Validator\Constraints\Regex(array(
                "pattern" => "/\W/",
                'message' => $this->translator->trans('Your Password need a minimum of 1 none word character', array(), 'validators')
            ));
        }


        return $constraints;
    }

    public function getDateFormater($format)
    {

        if (\is_string($format)) {
            $format = \constant('\IntlDateFormatter::' . \strtoupper($format));
        } else if (!\is_numeric($format)) {
            throw new Exception('Format must be an string or IntlDateFormater Int Value');
        }

        $locale = \Symfony\Component\Locale\Locale::getDefault();
        $tz = $this->getTimezone();

        $fmt = new \IntlDateFormatter(
            $locale, $format, $format, $tz->getName(), \IntlDateFormatter::GREGORIAN
        );
        return $fmt;
    }

    /**
     *
     * @param string $username
     * @return \Symbb\Core\UserBundle\Entity\UserInterface
     */
    public function findFields($criteria)
    {
        $fields = $this->em->getRepository('SymbbCoreUserBundle:Field', 'symbb')->findBy($criteria);
        return $fields;
    }

    public function createPagination($query, $page, $limit)
    {

        $rsm = new ResultSetMappingBuilder($this->emUser);
        $rsm->addScalarResult('count', 'count');

        $queryCount = $query->getSql();
        $queryCount = "SELECT COUNT(*) as count FROM (" . $queryCount . ") as temp";
        $queryCount = $this->emUser->createNativeQuery($queryCount, $rsm);
        $queryCount->setParameters($query->getParameters());
        $count = $queryCount->getSingleScalarResult();
        if (!$count) {
            $count = 0;
        }

        if ($limit === null) {
            $limit = 20;
        }

        if ($page === 'last') {
            $page = $count / (int)$limit;
            $page = ceil($page);
        }

        if ($page <= 0) {
            $page = 1;
        }

        $query->setHint('knp_paginator.count', $count);

        $pagination = $this->paginator->paginate(
            $query, (int)$page, $limit, array('distinct' => false)
        );

        return $pagination;
    }

    public function isGranted($access, $object, $identity = null)
    {
        return $this->securityContext->isGranted($access, $object, $identity);
    }

    /**
     * @return UserInterface
     */
    public function getGuestUser()
    {
        $user = $this->emUser->getRepository($this->config['usermanager']['user_class'])->findOneBy(array('symbbType' => 'guest'));
        return $user;
    }


    /**
     * @param UserInterface $user
     * @return mixed
     */
    public function getSymbbData(\Symbb\Core\UserBundle\Entity\UserInterface $user)
    {
        if (!isset($this->symbbDataCache[$user->getId()])) {
            $data = $this->em->getRepository('SymbbCoreUserBundle:User\Data', 'symbb')->findOneBy(array('userId' => $user->getId()));
            if (!is_object($data)) {
                $data = new User\Data();
                $data->setUser($user);
            }
            $this->symbbDataCache[$user->getId()] = $data;
        }
        return $this->symbbDataCache[$user->getId()];
    }


    /**
     * @param UserInterface $user
     * @return mixed
     */
    public function getFieldValues(\Symbb\Core\UserBundle\Entity\UserInterface $user)
    {
        $data = $this->em->getRepository('SymbbCoreUserBundle:User\Data', 'symbb')->findBy(array('userId' => $user->getId()));
        return $data;
    }

    /**
     * @param \Symbb\Core\UserBundle\Entity\Field $field
     * @param UserInterface $user
     * @return null|User\FieldValue
     */
    public function getFieldValue(\Symbb\Core\UserBundle\Entity\Field $field, \Symbb\Core\UserBundle\Entity\UserInterface $user)
    {
        $values = $this->getFieldValues($user);
        $found = null;
        foreach ($values as $value) {
            if ($value->getField()->getId() === $field->getId()) {
                $found = $value;
            }
        }
        if (!$found || !is_object($found)) {
            $found = new \Symbb\Core\UserBundle\Entity\User\FieldValue();
            $found->setField($field);
            $found->setUser($user);
        }

        return $found;
    }

}