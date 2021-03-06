<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SiteBundle\Controller;

use Symbb\Core\SystemBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class BackendApiController extends AbstractController
{

    /**
     * @Route("/api/sites", name="symbb_backend_api_site_list")
     * @Method({"GET"})
     */
    public function listAction()
    {
        $api = $this->get('symbb.core.api.site');
        $objects = $api->getList();

        $objectsData = array();
        foreach ($objects as $object) {
            $objectsData[] = $api->createArrayOfObject($object);
        }

        return $api->getJsonResponse(array(
            'data' => $objectsData
        ));
    }

    /**
     * @Route("/api/sites", name="symbb_backend_api_site_save")
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
     * @Route("/api/sites/{site}", name="symbb_backend_api_site_data")
     * @Method({"GET"})
     */
    public function findAction($site)
    {
        $api = $this->get('symbb.core.api.site');
        $api->find((int)$site);
        return $api->getJsonResponse();
    }

    /**
     * @Route("/api/sites/{site}", name="symbb_backend_api_site_delete")
     * @Method({"DELETE"})
     */
    public function deleteAction($site)
    {
        $api = $this->get('symbb.core.api.site');
        $api->delete((int)$site);
        return $api->getJsonResponse();
    }

    /**
     * @Route("/api/sites/{site}/navigations", name="symbb_backend_api_site_navigation_list")
     * @Method({"GET"})
     */
    public function findNavigations($site)
    {
        $api = $this->get('symbb.core.api.site.navigation');
        $objects = $api->findAll($site);

        $objectsData = array();
        foreach ($objects as $object) {
            $objectsData[] = $api->createArrayOfObject($object);
        }

        return $api->getJsonResponse(array(
            'data' => $objectsData
        ));
    }


    /**
     * @Route("/api/sites/{site}/navigations", name="symbb_backend_api_site_navigation_save")
     * @Method({"POST"})
     */
    public function saveNavigation($site, Request $request)
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
     * @Route("/api/sites/{site}/navigations/{navigation}", name="symbb_backend_api_site_navigation_delete")
     * @Method({"DELETE"})
     */
    public function deleteNavigation($site, $navigation, Request $request)
    {
        $api = $this->get('symbb.core.api.site.navigation');
        $api->delete((int)$navigation);
        return $api->getJsonResponse(array());
    }

    /**
     * @Route("/api/sites/{site}/navigations/{navigation}/items", name="symbb_backend_api_site_navigation_item_save")
     * @Method({"POST"})
     */
    public function saveNavigationItem($site, $navigation, Request $request)
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
     * @Route("/api/sites/{site}/navigations/{navigation}/items/{item}", name="symbb_backend_api_site_navigation_item_delete")
     * @Method({"DELETE"})
     */
    public function deleteNavigationItem($site, $navigation, $item, Request $request)
    {
        $api = $this->get('symbb.core.api.site.navigation');
        $api->deleteItem((int)$item);
        return $api->getJsonResponse(array());
    }

}