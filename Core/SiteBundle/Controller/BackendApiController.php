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
        $data = $request->get('data');
        $site = $api->save($data);
        $site = $api->createArrayOfObject($site);
        return $api->getJsonResponse(array(
            'data' => $site
        ));
    }

    /**
     * @Route("/api/site/delete", name="symbb_backend_api_site_delete")
     * @Method({"DELETE"})
     */
    public function deleteAction(Request $request)
    {
        $api = $this->get('symbb.core.api.site');
        $data = $request->get('data');
        $api->delete((int)$data);
        return $api->getJsonResponse();
    }

    /**
     * @Route("/api/site/navigation/save", name="symbb_backend_api_site_navigation_save")
     * @Method({"POST"})
     */
    public function saveNavigation(Request $request)
    {
        $api = $this->get('symbb.core.api.site.navigation');
        $data = $request->get('data');
        $object = $api->save($data);
        $object = $api->createArrayOfObject($object);
        return $api->getJsonResponse(array(
            'data' => $object
        ));
    }

    /**
     * @Route("/api/site/navigation/delete", name="symbb_backend_api_site_navigation_delete")
     * @Method({"DELETE"})
     */
    public function deleteNavigation(Request $request)
    {
        $api = $this->get('symbb.core.api.site.navigation');
        $data = (int)$request->get('data');
        $api->delete($data);
        return $api->getJsonResponse(array(
        ));
    }

    /**
     * @Route("/api/site/navigation/item/save", name="symbb_backend_api_site_navigation_item_save")
     * @Method({"POST"})
     */
    public function saveNavigationItem(Request $request)
    {
        $api = $this->get('symbb.core.api.site.navigation');
        $data = $request->get('data');
        $object = $api->saveItem($data);
        $object = $api->createArrayOfObject($object);
        return $api->getJsonResponse(array(
            'data' => $object
        ));
    }

    /**
     * @Route("/api/site/navigation/item/delete", name="symbb_backend_api_site_navigation_item_delete")
     * @Method({"DELETE"})
     */
    public function deleteNavigationItem(Request $request)
    {
        $api = $this->get('symbb.core.api.site.navigation');
        $data = (int)$request->get('data');
        $api->deleteItem($data);
        return $api->getJsonResponse(array(
        ));
    }

}