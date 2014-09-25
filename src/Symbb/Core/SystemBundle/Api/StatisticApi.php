<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SystemBundle\Api;

use Symbb\Core\UserBundle\Entity\UserInterface;
use Symfony\Component\HttpFoundation\Request;

class StatisticApi extends AbstractApi
{

    protected $memcache;
    const MEMCACHE_VISITOR_KEY = 'symbb_backen_api_statictic_vistors';

    /**
     * @param $memcache
     */
    public function setMemcache($memcache){
        $this->memcache = $memcache;
    }

    /**
     * get the count of all posts in the system
     * @return int
     */
    public function getPostCount(){
        $entries = $this->em->getRepository('SymbbCoreForumBundle:Post')->findAll();
        return count($entries);
    }


    /**
     * get the count of all topics systems
     * @return int
     */
    public function getTopicCount(){
        $entries = $this->em->getRepository('SymbbCoreForumBundle:Topic')->findAll();
        return count($entries);
    }


    /**
     * get the number of active users
     * @return int
     */
    public function getActiveUserCount(){
        $users = $this->userManager->findUsers();
        return count($users);
    }


    /**
     * @todo
     * @return int
     */
    public function getInactiveUserCount(){
        return 0;
    }

    /**
     * this method will add a visitor to the statistic
     * currently its only a memcache storage because this information
     * is not important if someone will track his visitors fully he should use a analytics tool
     *
     * @param UserInterface $user
     * @param Request $request
     */
    public function addVisitor(UserInterface $user, Request $request){

        $days = 30;
        $ip = $request->getClientIp();
        $data = $this->getVisitors();
        $date = new \DateTime();
        $currTimestamp = $date->getTimestamp();
        // entries should be only hourly
        $date->setTime($date->format('H'),0,0);
        $todayTimestamp = $date->getTimestamp();

        if(!isset($data[$todayTimestamp])){
            $data[$todayTimestamp] = array();
        }

        $type = $user->getSymbbType();

        $currVisitor = array(
            'id' => $user->getId(),
            'type' => $type,
            'ip' => $ip,
            'lastVisiting' => $currTimestamp
        );

        $overwriteKey = null;

        // remove entries who are to old
        $maxOldDate = new \DateTime();
        $maxOldDate->setTime(0,0,0);
        $maxOldDate->modify('-'.$days.'days');
        foreach($data as $timestamp => $tmp){
            if(!is_numeric($timestamp) || $timestamp < $maxOldDate->getTimestamp()){
                unset($data[$timestamp]);
            }
        }

        foreach($data[$todayTimestamp] as $key => $visitor){

            if(
                // case 1: guest -> check ip
                (
                    $currVisitor['type'] == 'guest' &&
                    $visitor['type'] == $currVisitor['type'] &&
                    $visitor['ip'] == $currVisitor['ip']
                ) ||
                // case 2: user -> check id ( can login with different ips )
                (
                    $currVisitor['type'] == 'user' &&
                    $visitor['type'] == $currVisitor['type'] &&
                    $visitor['id'] == $currVisitor['id']
                )
            ){
                $overwriteKey = $key;
                break;
            }
        }

        // if we have found the same visitor in the data
        // then we will overwrite it
        // if not we will add the vistor
        if($overwriteKey !== null){
            $data[$todayTimestamp][$overwriteKey] = $currVisitor;
        } else {
            $data[$todayTimestamp][] = $currVisitor;
        }

        // sort
        ksort($data, SORT_NUMERIC);
        // reverse so that the newest are the first
        $data = array_reverse($data, true);

        $this->memcache->set(self::MEMCACHE_VISITOR_KEY, $data, (86400 * $days)); // 30 days valid
    }

    /**
     * get a list of the visitors
     * it is a array, the firs level key ist a timestamp (server timezone) of the Day ( without h/m/s )
     * the next level is also a array with all visitors
     * one visitor has the information id/type/ip/lastVisiting
     * @return array
     */
    public function getVisitors(){
        $data = $this->memcache->get(self::MEMCACHE_VISITOR_KEY);
        if(!$data){
            $data = array();
        }
        return (array)$data;
    }
}