<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\UserBundle\Controller;

use Symbb\Core\SystemBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class BackendApiController extends AbstractController
{

    /**
     * @Route("/api/user", name="symbb_backend_api_user_list")
     * @Method({"GET"})
     */
    public function listAction(Request $request)
    {
        $api = $this->get('symbb.core.api.user');
        $sites = $api->getList($request->get('limit', 20), $request->get('page', 1));
        $sites = $api->createArrayOfObject($sites);

        return $api->getJsonResponse(array(
            'data' => $sites
        ));
    }

    /**
     * @Route("/api/user", name="symbb_backend_api_user_save")
     * @Method({"POST"})
     */
    public function saveAction(Request $request)
    {
        $api = $this->get('symbb.core.api.user');
        $data = $request->get('data');
        $site = $api->save($data);
        $site = $api->createArrayOfObject($site);
        return $api->getJsonResponse(array(
            'data' => $site
        ));
    }

    /**
     * @Route("/api/user/{user}", name="symbb_backend_api_user_data")
     * @Method({"GET"})
     */
    public function findAction($user)
    {
        $api = $this->get('symbb.core.api.user');
        $api->find((int)$user);
        return $api->getJsonResponse();
    }

    /**
     * @Route("/api/user/{user}", name="symbb_backend_api_user_delete")
     * @Method({"DELETE"})
     */
    public function deleteAction($user)
    {
        $api = $this->get('symbb.core.api.user');
        $api->delete((int)$user);
        return $api->getJsonResponse();
    }

    /**
     * @Route("/api/usergroup", name="symbb_backend_api_user_group_list")
     * @Method({"GET"})
     */
    public function groupListAction(Request $request)
    {
        $api = $this->get('symbb.core.api.user.group');
        $objects = $api->getList($request->get('limit', 20), $request->get('page', 1));
        $objects = $api->createArrayOfObject($objects);
        return $api->getJsonResponse(array(
            'data' => $objects
        ));
    }

    /**
     * @Route("/api/usergroup", name="symbb_backend_api_user_group_save")
     * @Method({"POST"})
     */
    public function saveGroupAction(Request $request)
    {
        $api = $this->get('symbb.core.api.user.group');
        $data = $request->get('data');
        $object = $api->save($data);
        $object = $api->createArrayOfObject($object);
        return $api->getJsonResponse(array(
            'data' => $object
        ));
    }

    /**
     * @Route("/api/usergroup/{group}", name="symbb_backend_api_user_group_data")
     * @Method({"GET"})
     */
    public function findGroupAction($group)
    {
        $api = $this->get('symbb.core.api.user.group');
        $api->find((int)$group);
        return $api->getJsonResponse();
    }

    /**
     * @Route("/api/usergroup/{group}", name="symbb_backend_api_user_group_delete")
     * @Method({"DELETE"})
     */
    public function deleteGroupAction($group)
    {
        $api = $this->get('symbb.core.api.user.group');
        $api->delete((int)$group);
        return $api->getJsonResponse();
    }

    /**
     * @Route("/api/userfield", name="symbb_backend_api_user_field_list")
     * @Method({"GET"})
     */
    public function fieldListAction(Request $request)
    {
        $api = $this->get('symbb.core.api.user.field');
        $objects = $api->findAll($request->get('limit', 20), $request->get('page', 1));
        $objects = $api->createArrayOfObject($objects);
        return $api->getJsonResponse(array(
                'data' => $objects
            ));
    }

    /**
     * @Route("/api/userfield", name="symbb_backend_api_user_field_save")
     * @Method({"POST"})
     */
    public function saveFieldAction(Request $request)
    {
        $api = $this->get('symbb.core.api.user.field');
        $data = $request->get('data');
        $object = $api->save($data);
        $object = $api->createArrayOfObject($object);
        return $api->getJsonResponse(array(
                'data' => $object
            ));
    }

    /**
     * @Route("/api/userfield/{field}", name="symbb_backend_api_user_field_data")
     * @Method({"GET"})
     */
    public function findFieldAction($field)
    {
        $api = $this->get('symbb.core.api.user.field');
        $api->find((int)$field);
        return $api->getJsonResponse();
    }

    /**
     * @Route("/api/userfield/{field}", name="symbb_backend_api_user_field_delete")
     * @Method({"DELETE"})
     */
    public function deleteFieldAction($field)
    {
        $api = $this->get('symbb.core.api.user.field');
        $api->delete((int)$field);
        return $api->getJsonResponse();
    }
}