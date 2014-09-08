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
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class BackendApiController extends AbstractController
{

    /**
     * @Route("/api/site/list", name="symbb_backend_api_site_list")
     * @Method({"GET"})
     */
    public function listAction()
    {
        $api = $this->get('symbb.core.api.site');
        $sites = $api->getList();
        $sites = $api->createArrayOfObject($sites);

        return $api->getJsonResponse(array(
            'data' => $sites
        ));
    }

    /**
     * @Route("/api/site/save", name="symbb_backend_api_site_save")
     * @Method({"POST"})
     */
    public function saveAction(Request $request)
    {
        $api = $this->get('symbb.core.api.site');
        $site = $api-save($request->get('data'));
        $site = $this->serializer->deserialize($site, 'SymBB\Core\SiteBundle\Entity\Site', 'json');

        return $api->getJsonResponse(array(
            'data' => $site
        ));
    }
}