<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SiteBundle\Controller;

use SymBB\Core\SystemBundle\Controller\AbstractController;

class BackendApiController extends AbstractController
{
    public function listAction()
    {
        $api = $this->get('symbb.core.api.site');
        $sites = $api->getSites();

        return $api->getJsonResponse(array(
            'data' => $sites
        ));
    }
}