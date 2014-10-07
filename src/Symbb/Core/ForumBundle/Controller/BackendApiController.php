<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Controller;

use Symbb\Core\SystemBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class BackendApiController extends AbstractController
{

    /**
     * @Route("/api/forum", name="symbb_backend_api_forum_list")
     * @Method({"GET"})
     */
    public function listAction(Request $request)
    {
        $api = $this->get('symbb.core.api.forum');
        $objects = $api->findAll($request->get('parent', 0), $request->get('limit', 20), $request->get('page', 1));

        $objectsData = array();
        foreach($objects as $object){
            $objectsData[] = $api->createArrayOfObject($object);
        }

        return $api->getJsonResponse(array(
            'data' => $objectsData
        ));
    }

    /**
     * @Route("/api/forum", name="symbb_backend_api_forum_save")
     * @Method({"POST"})
     */
    public function saveAction(Request $request)
    {
        $api = $this->get('symbb.core.api.forum');
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
        $api = $this->get('symbb.core.api.forum');
        $api->find((int)$user);
        return $api->getJsonResponse();
    }

    /**
     * @Route("/api/forum/{forum}", name="symbb_backend_api_forum_delete")
     * @Method({"DELETE"})
     */
    public function deleteAction($forum)
    {
        $api = $this->get('symbb.core.api.forum');
        $api->delete((int)$forum);
        return $api->getJsonResponse();
    }

}