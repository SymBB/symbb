<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\UserBundle\Twig;

class UserDataExtension extends \Twig_Extension
{
    protected $userManager;
    
    public function __construct(\SymBB\Core\UserBundle\DependencyInjection\UserManager $userManager)
    {
    
        $this->userManager = $userManager;

    }
    
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getSymbbUserData', array($this, 'getSymbbUserData')),
            new \Twig_SimpleFunction('getSymbbUserAvatar', array($this, 'getSymbbUserAvatar'), array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('getSymbbUserSignature', array($this, 'getSymbbUserSignature'), array(
                'is_safe' => array('html')
            ))
        );
    }
    
    public function getSymbbUserData(\SymBB\Core\UserBundle\Entity\UserInterface $user)
    {
        $data = $user->getSymbbData();
        return $data;
    }
    
    public function getSymbbUserSignature(\SymBB\Core\UserBundle\Entity\UserInterface $user)
    {
        return $this->userManager->getSignature($user);
    }
    
    public function getSymbbUserAvatar(\SymBB\Core\UserBundle\Entity\UserInterface $user)
    {
        return $this->userManager->getAvatar($user);
    }

    public function getName()
    {
        return 'symbb_user_data';
    }
}