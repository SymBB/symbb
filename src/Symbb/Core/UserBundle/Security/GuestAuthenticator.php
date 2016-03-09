<?php

namespace Symbb\Core\UserBundle\Security;

use Symbb\Core\UserBundle\Entity\User;
use Symbb\Core\UserBundle\Manager\UserManager;
use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

class GuestAuthenticator implements SimplePreAuthenticatorInterface
{
    protected $em;

    protected $config;

    public function __construct($container)
    {
        $config = $container->getParameter('symbb_config');
        $this->em = $container->get('doctrine.orm.'.$config['usermanager']['entity_manager'].'_entity_manager');
        $this->config = $config;
    }

    public function createToken(Request $request, $providerKey)
    {
        return new PreAuthenticatedToken(
            'anon.',
            "symbb_guest",
            $providerKey
        );
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $user = $this->em->getRepository($this->config['usermanager']['user_class'])->findOneBy(array('symbbType' => 'guest'));
        if(is_object($user)){
            $roles = $user->getRoles();
        } else {
            throw new \Exception('please load the initial fixtures!');
        }
        return new PreAuthenticatedToken(
            $user,
            "symbb_guest",
            $providerKey,
            $roles
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }
}