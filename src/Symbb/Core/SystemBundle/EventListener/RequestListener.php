<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SystemBundle\EventListener;

use Symbb\Core\SystemBundle\Api\StatisticApi;
use Symbb\Core\UserBundle\Manager\UserManager;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;

class RequestListener
{

    /**
     * @var StatisticApi $statisticApi
     */
    protected $statisticApi;

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    public function __construct(StatisticApi $api, SecurityContextInterface $securityContext){
        $this->statisticApi = $api;
        $this->securityContext = $securityContext;
    }

    public function statistic(FinishRequestEvent $event)
    {
        if($event->isMasterRequest()){
            $request = $event->getRequest();
            $token = $this->securityContext->getToken();
            if($token){
                $this->statisticApi->addVisitor($token->getUser(), $request);
            }
        }
    }
}