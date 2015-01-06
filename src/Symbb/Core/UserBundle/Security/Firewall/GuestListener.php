<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace Symbb\Core\UserBundle\Security\Firewall;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

class GuestListener implements ListenerInterface
{
    private $em;

    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $authenticationManager, $em)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->em      = $em;
    }
    
    /**
     * Handles anonymous authentication.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance
     */
    public function handle(GetResponseEvent $event)
    {
        $token = $this->tokenStorage->getToken();
        if (null !== $token) {
            return;
        }

        $user = $this->em->getRepository('SymbbCoreUserBundle:User', 'symbb')->findOneBy(array('symbbType' => 'guest'));
        
        if(\is_object($user)){
            $token = new AnonymousToken($this->key, $user, array());
            $authToken = $this->authenticationManager->authenticate($token);
            $this->tokenStorage->setToken($authToken);
        }
    }
}