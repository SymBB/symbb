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
use Symbb\Core\UserBundle\Entity\User\Data;
use \Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use \Doctrine\ORM\EntityManager;
use \Symbb\Core\UserBundle\Entity\UserInterface;

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

    /**
     *
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected $securityContext;

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
        $this->em = $container->get('doctrine.orm.symbb_entity_manager');
        $this->securityFactory = $container->get('security.encoder_factory');
        $config = $container->getParameter('symbb_config');
        $this->config = $config['usermanager'];
        $this->userClass = $this->config['user_class'];
        $this->paginator = $container->get('knp_paginator');
        $this->securityContext = $container->get('security.context');
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
        return $this->securityContext->getToken()->getUser();
    }

    /**
     * update the given user
     * @param \Symbb\Core\UserBundle\Entity\UserInterface $user
     * @return bool
     */
    public function updateUser(UserInterface $user)
    {
        $user->setChangedValue();
        $this->em->persist($user);
        $this->em->flush();
        return true;
    }

    /**
     * update the given user data
     * @param \Symbb\Core\UserBundle\Entity\User\Data $user
     */
    public function updateUserData(Data $data)
    {
        $this->em->persist($data);
        $this->em->flush();

        //return array with sf validator errors
        return new \Symfony\Component\Validator\ConstraintViolationList();
    }

    /**
     * @param UserInterface $user
     * @return bool
     */
    public function removeUser(UserInterface $user)
    {
        $this->em->remove($user);
        $this->em->flush();
        return true;
    }

    /**
     * change the password of an user
     * @param \Symbb\Core\UserBundle\Entity\UserInterface $user
     * @param string $newPassword
     */
    public function changeUserPassword(UserInterface $user, $newPassword)
    {
        $user->setChangedValue();
        $encoder = $this->securityFactory->getEncoder($user);
        $password = $encoder->encodePassword($newPassword, $user->getSalt());
        $user->setPassword($password);
        $validator = $this->container->get('validator');
        $passwordConstraints = $this->getPasswordValidatorConstraints();
        $passwordConstraints[] = new \Symfony\Component\Validator\Constraints\NotBlank();
        $errorsPassword = $validator->validateValue($newPassword, $passwordConstraints);

        if($errorsPassword->count() === 0){
            $this->em->persist($user);
            $this->em->flush();
        }

        return $errorsPassword;
    }

    /**
     * create a new User
     * @return UserInterface
     */
    public function createUser()
    {
        $userClass = $this->userClass;
        $user = new $userClass();
        return $user;
    }

    /**
     * 
     * @param type $userId
     * @return \Symbb\Core\UserBundle\Entity\UserInterface
     */
    public function find($userId)
    {
        $user = $this->em->getRepository($this->userClass)->find($userId);
        return $user;
    }

    /**
     * 
     * @param string $username
     * @return \Symbb\Core\UserBundle\Entity\UserInterface
     */
    public function findByUsername($username)
    {
        $user = $this->em->getRepository($this->userClass)->findOneBy(array('username' => $username));
        return $user;
    }

    /**
     * 
     * @return array(<"\Symbb\Core\UserBundle\Entity\UserInterface">)
     */
    public function findUsers($limit = 20 , $page = 1)
    {
        $users = $this->findBy(array(), $limit, $page);
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

        $qb = $this->em->getRepository($this->userClass)->createQueryBuilder('u');
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
        if(!empty($whereParts)){
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

        $data = $this->getSymbbData($user);
        $avatar = $data->getAvatar();
        if (empty($avatar)) {
            $avatar = '/bundles/symbbtemplatedefault/images/avatar/empty.gif';
        }
        return $avatar;
    }

    public function getPostCount(\Symbb\Core\UserBundle\Entity\UserInterface $user = null)
    {
        if (!$user) {
            $user = $this->getCurrentUser();
        }
        
        if (!isset($this->postCountCache[$user->getId()])) {
            $qb = $this->em->getRepository('SymbbCoreForumBundle:Post')->createQueryBuilder('p');
            $qb->select('COUNT(p.id)');
            $qb->where("p.author = " . $user->getId());
            $count = $qb->getQuery()->getSingleScalarResult();
            $this->postCountCache[$user->getId()] = $count;
        } else {
            $count = $this->postCountCache[$user->getId()];
        }
        return $count;
    }

    public function getLastPosts(\Symbb\Core\UserBundle\Entity\UserInterface $user = null, $limit = 20)
    {
        if (!$user) {
            $user = $this->getCurrentUser();
        }

        $qb = $this->em->getRepository('SymbbCoreForumBundle:Post')->createQueryBuilder('p');
        $qb->select('p');
        $qb->where("p.author = " . $user->getId());
        $qb->orderBy("p.created", "desc");
        $dql = $qb->getDql();
        $query = $this->em->createQuery($dql);
        $posts = $this->createPagination($query, 1, $limit);
        return $posts;
    }

    public function getTopicCount(\Symbb\Core\UserBundle\Entity\UserInterface $user = null)
    {
        if (!$user) {
            $user = $this->getCurrentUser();
        }

        $qb = $this->em->getRepository('SymbbCoreForumBundle:Topic')->createQueryBuilder('p');
        $qb->select('COUNT(p.id)');
        $qb->where("p.author = " . $user->getId());
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

            $this->securityContext->setToken($token);

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
        $fields = $this->em->getRepository('SymbbCoreUserBundle:Field')->findBy($criteria);
        return $fields;
    }

    public function getSymbbData(\Symbb\Core\UserBundle\Entity\UserInterface $user)
    {
        if (!isset($this->symbbDataCache[$user->getId()])) {
            $this->symbbDataCache[$user->getId()] = $user->getSymbbData();
        }
        return $this->symbbDataCache[$user->getId()];
    }


    public function createPagination($query, $page, $limit){

        $rsm = new ResultSetMappingBuilder($this->em);
        $rsm->addScalarResult('count', 'count');

        $queryCount = $query->getSql();
        $queryCount = "SELECT COUNT(*) as count FROM (".$queryCount.") as temp";
        $queryCount = $this->em->createNativeQuery($queryCount, $rsm);
        $queryCount->setParameters($query->getParameters());
        $count = $queryCount->getSingleScalarResult();
        if(!$count){
            $count = 0;
        }

        if(!$limit){
            $limit = 20;
        }

        if($page === 'last'){
            $page = $count / $limit;
            $page = ceil($page);
        }

        if($page <= 0){
            $page = 1;
        }

        $query->setHint('knp_paginator.count', $count);

        $pagination = $this->paginator->paginate(
            $query, (int)$page, $limit, array('distinct' => false)
        );

        return $pagination;
    }
}