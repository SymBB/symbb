<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SystemBundle\Controller;

use SymBB\Core\SystemBundle\DependencyInjection\StatisticApi;

class BackendApiController extends AbstractController
{
    /**
     * get some dashboard summary data like statistics etc..
     * so that we dont need to make seperate requests
     * @return \Symfony\Component\HttpFoundation\Response
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

        return $statisticApi->getJsonResponse(array(
            'statistic' => array(
                'countData' => $countData
            )
        ));
    }
}