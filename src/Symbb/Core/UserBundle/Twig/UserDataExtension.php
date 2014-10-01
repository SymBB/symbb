<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\UserBundle\Twig;

use Symbb\Core\UserBundle\DependencyInjection\GroupManager;
use Symbb\Core\UserBundle\DependencyInjection\UserManager;

class UserDataExtension extends \Twig_Extension
{

    protected $userManager;
    protected $groupManager;

    /**
     * @param UserManager $userManager
     * @param GroupManager $groupManager
     */
    public function __construct(UserManager $userManager, GroupManager $groupManager)
    {

        $this->userManager = $userManager;
        $this->groupManager = $groupManager;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getSymbbUserData', array($this, 'getSymbbUserData')),
            new \Twig_SimpleFunction('isSymbbGuest', array($this, 'isSymbbGuest')),
            new \Twig_SimpleFunction('getUserManager', array($this, 'getUserManager')),
            new \Twig_SimpleFunction('getGroupManager', array($this, 'getGroupManager')),
            new \Twig_SimpleFunction('getSymbbUserAvatar', array($this, 'getSymbbUserAvatar'), array(
                'is_safe' => array('html')
                )),
            new \Twig_SimpleFunction('getSymbbUserSignature', array($this, 'getSymbbUserSignature'), array(
                'is_safe' => array('html')
                ))
        );
    }

    public function getUserManager()
    {
        return $this->userManager;
    }


    public function getGroupManager()
    {
        return $this->groupManager;
    }

    public function getSymbbUserData(\Symbb\Core\UserBundle\Entity\UserInterface $user)
    {
        $data = $this->userManager->getSymbbData($user);
        return $data;
    }

    public function getSymbbUserSignature(\Symbb\Core\UserBundle\Entity\UserInterface $user)
    {
        return $this->userManager->getSignature($user);
    }

    public function isSymbbGuest(\Symbb\Core\UserBundle\Entity\UserInterface $user)
    {
        if ($user->getSymbbType() == 'guest') {
            return true;
        }
        return false;
    }

    public function getSymbbUserAvatar(\Symbb\Core\UserBundle\Entity\UserInterface $user)
    {
        return $this->userManager->getAvatar($user);
    }

    public function getName()
    {
        return 'symbb_user_data';
    }
}