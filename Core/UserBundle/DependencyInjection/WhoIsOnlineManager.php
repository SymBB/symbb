<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\UserBundle\DependencyInjection;

use \SymBB\Core\UserBundle\Entity\UserInterface;
use \SymBB\Core\UserBundle\DependencyInjection\UserManager;

class WhoIsOnlineManager
{
      
    /**
     * @var UserManager 
     */
    protected $userManager;
    
    /**
     *
     * @var \Symfony\Component\Security\Core\SecurityContextInterface 
     */
    protected $securityContext;
    
    protected $memcache;
    
    const LIFETIME = 86400; // 1day

    const CACHE_KEY = 'WHO_IS_ONLINE';

    /**
     *
     * @var UserInterface
     */
    protected $user;

    public function __construct(UserManager $userManager, $securityContext, $memcache) {
        $this->userManager      = $userManager;
        $this->securityContext  = $securityContext;
        $this->memcache         = $memcache;
    }
    
    public function getUser(){
        if(!is_object($this->user)){
            $this->user = $this->securityContext->getToken()->getUser();
        }
        return $this->user;
    }
    
    public function addCurrent($request){
        $user = $this->getUser();
        $this->addUser($user, $request->getClientIp());
    }
    
    public function addUser(UserInterface $user, $ip){
        
        $userlist       = $this->getUserlist();
        $now            = time();
        $currUserFound  = false;
        
        foreach($userlist as $key => $onlineUserData){
            
            if($onlineUserData['id'] != $user->getId()){
                $diff = $now - $onlineUserData['added'];
                if($diff > 300){
                    unset($userlist[$key]);
                }
            } else {
                $currUserFound = true;
                $userlist[$key]['added'] = $now;
                $ips    = $onlineUserData['ips'];
                $ips[]  = $ip;
                $ips    = array_unique($ips);
                $userlist[$key]['ips']      = $ips;
                $userlist[$key]['count']    = count($ips);
            }
            
        }
        
        if(!$currUserFound){
            $count      = 1;
            $ips        = array($ip);
            $userlist[] = array(
                'id'        => $user->getId(),
                'added'     => $now,
                'username'  => $user->getUsername(),
                'type'      => $user->getSymbbType(),
                'count'     => $count,
                'ips'       => $ips
            );
        }
        
        
        $this->memcache->set(self::CACHE_KEY, $userlist, self::LIFETIME);
    }
    
    public function getUserlist(){
        $userlist = $this->memcache->get(self::CACHE_KEY);
        
        if(!$userlist){
            $userlist = array();
        }
        
        return $userlist;
    }
}