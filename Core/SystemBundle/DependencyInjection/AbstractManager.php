<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SystemBundle\DependencyInjection;

use \Symfony\Component\Security\Core\SecurityContextInterface;
use \SymBB\Core\SystemBundle\DependencyInjection\ConfigManager;

abstract class AbstractManager
{

    /**
     *
     * @var SecurityContextInterface 
     */
    protected $securityContext;

    /**
     * @var UserInterface
     */
    protected $user;

    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;

    }

    /**
     * 
     * @return type
     */
    public function getUser()
    {
        if (!is_object($this->user)) {
            $this->user = $this->securityContext->getToken()->getUser();
        }
        return $this->user;

    }
}
