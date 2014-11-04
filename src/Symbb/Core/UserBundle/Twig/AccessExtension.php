<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace Symbb\Core\UserBundle\Twig;

use Symbb\Core\SystemBundle\Manager\AccessManager;

class AccessExtension extends \Twig_Extension
{
    /**
     *
     * @var AccessManager 
     */
    protected $accessManager;

    protected $securityContext;
    
    public function __construct(AccessManager $accessManager, $securityContext) {
        $this->accessManager = $accessManager;
        $this->securityContext = $securityContext;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('hasSymbbAccess', array($this, 'hasSymbbAccess'))
        );
    }
    
    public function hasSymbbAccess( $access, $element, $user = null)
    {
        $access = $this->securityContext->isGranted($access, $element, $user);
        return $access;
    }
    

    public function getName()
    {
        return 'symbb_user_access';
    }
}