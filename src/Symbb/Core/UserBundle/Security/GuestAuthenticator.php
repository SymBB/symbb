<?php

namespace Symbb\Core\UserBundle\Security;

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

    public function __construct($em)
    {
        $this->em = $em;
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
        $user = $this->em->getRepository('SymbbCoreUserBundle:User', 'symbb')->findOneBy(array('symbbType' => 'guest'));
        $roles = array();
        if(is_object($user)){
            $roles = $user->getRoles();
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