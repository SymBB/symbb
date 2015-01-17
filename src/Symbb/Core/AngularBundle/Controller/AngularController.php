<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\AngularBundle\Controller;

use Symbb\Core\AngularBundle\DependencyInjection\Router;
use Symbb\Core\AngularBundle\Routing\AngularRoute;
use Symfony\Component\HttpFoundation\Request;

class AngularController extends \Symbb\Core\SystemBundle\Controller\AbstractController
{
    public function indexAction(Request $request)
    {
        $userAgent = $request->server->get("HTTP_USER_AGENT");

        if (strpos($userAgent, 'Google') !== false || $request->get('seo') == 1) {
            return $this->seoAction($request);
        } else {
            return $this->render($this->getTemplateBundleName('forum') . ':Forum:index.html.twig', array());
        }

    }

}