<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\UserBundle\Security\Firewall;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use \Symfony\Component\Security\Http\Firewall\AnonymousAuthenticationListener;
use Symfony\Component\Security\Core\SecurityContextInterface;
use \Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

class GuestListener extends AnonymousAuthenticationListener
{
    private $em;

    public function __construct(SecurityContextInterface $context, $key, \Psr\Log\LoggerInterface $logger = null, $em)
    {
        parent::__construct($context, $key);
        $this->context = $context;
        $this->key     = $key;
        $this->logger  = $logger;
        $this->em      = $em;
    }
    
    /**
     * Handles anonymous authentication.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance
     */
    public function handle(GetResponseEvent $event)
    {

        if (null !== $this->context->getToken()) {
            return;
        }

        $user = $this->em->getRepository('SymBBCoreUserBundle:User', 'symbb')->findOneBy(array('symbbType' => 'guest'));
        
        if(\is_object($user)){
           
            $this->context->setToken(new AnonymousToken($this->key, $user, array()));

            if (null !== $this->logger) {
                $this->logger->info('Populated SecurityContext with an anonymous Token');
            }
        }
    }
}