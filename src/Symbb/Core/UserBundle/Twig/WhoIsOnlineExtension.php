<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\UserBundle\Twig;

class WhoIsOnlineExtension extends \Twig_Extension
{

    protected $whoIsOnlineManager;
    
    public function __construct(\SymBB\Core\UserBundle\DependencyInjection\WhoIsOnlineManager $whoIsOnlineManager) {
        $this->whoIsOnlineManager = $whoIsOnlineManager;
    }
    
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getSymbbOnlineUsers', array($this, 'getSymbbOnlineUsers')),
            new \Twig_SimpleFunction('getSymbbOnlineUserCount', array($this, 'getSymbbOnlineUserCount'))
        );
    }
    
    public function getSymbbOnlineUsers()
    {
        $data = $this->whoIsOnlineManager->getUserlist();
        return $data;
    }
    
    public function getSymbbOnlineUserCount($type = null)
    {
        $data = $this->whoIsOnlineManager->getUserlist();
        $count = 0;
        foreach($data as $user){
            if(
                ($type && $user['type'] == $type) ||
                !$type
            ){
                $count = $count + $user['count'];
            }
        }
        return $count;
    }

    public function getName()
    {
        return 'symbb_user_whoisonline';
    }
}