<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SystemBundle\Controller;

use SymBB\Core\SystemBundle\Api\StatisticApi;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class BackendApiController extends AbstractController
{
    /**
     * @Route("/api/dashboard/data", name="symbb_backend_api_dashboard_data")
     * @Method({"GET"})
     */
    public function dashboardDataAction()
    {
        $statisticApi = $this->get('symbb.core.api.statistic');
        /** @var StatisticApi $statisticApi */

        $countData = array();
        $countData['post'] = $statisticApi->getPostCount();
        $countData['topic'] = $statisticApi->getTopicCount();
        $countData['activeUser'] = $statisticApi->getActiveUserCount();
        $countData['inactiveUser'] = $statisticApi->getInactiveUserCount();
        $countData['inactiveUser'] = $statisticApi->getInactiveUserCount();
        $visitors = $statisticApi->getVisitors();

        $userVisitors = array();
        $guestVisitors = array();
        $date = new \DateTime();

        foreach($visitors as $dayTimestamp => $visitorList){
            $date->setTimestamp($dayTimestamp);
            $dayTimestamp = $statisticApi->getISO8601ForUser($date);
            $userVisitors[$dayTimestamp] = array();
            $guestVisitors[$dayTimestamp] = array();
            foreach($visitorList as $visitor){
                if($visitor['type'] == 'user'){
                    $userVisitors[$dayTimestamp][] = $visitor;
                } else {
                    $guestVisitors[$dayTimestamp][] = $visitor;
                }
            }
        }

        return $statisticApi->getJsonResponse(array(
            'statistic' => array(
                'countData' => $countData,
                'visitors' => array(
                    'users' => $userVisitors,
                    'guests' => $guestVisitors
                )
            )
        ));
    }
}