<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\UserBundle\Twig;

use SymBB\Core\SystemBundle\DependencyInjection\AccessManager;

class AccessExtension extends \Twig_Extension
{
    /**
     *
     * @var AccessManager 
     */
    protected $accessManager;
    
    public function __construct(AccessManager $accessManager) {
        $this->accessManager = $accessManager;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('hasSymbbAccess', array($this, 'hasSymbbAccess'))
        );
    }
    
    public function hasSymbbAccess( $access, $element, $user = null)
    {
        $this->accessManager->addAccessCheck($access, $element, $user);
        $access             = $this->accessManager->hasAccess();
        return $access;
    }
    

    public function getName()
    {
        return 'symbb_user_access';
    }
}