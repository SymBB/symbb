<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Controller;

use Symbb\Core\SystemBundle\Api\AbstractApi;
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
        $api->entityAccessCheck = false;

        $objects = $api->findAll(null, $request->get('limit', 999), $request->get('page', 1));

        $objectsData = array();
        foreach ($objects as $object) {
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
        $api->entityAccessCheck = false;

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
        $api->entityAccessCheck = false;

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
        $api->entityAccessCheck = false;

        $api->delete((int)$forum);
        return $api->getJsonResponse();
    }

    /**
     * @Route("/api/forum/copyAccess", name="symbb_backend_api_forum_copy_access")
     * @Method({"POST"})
     */
    public function copyAccessAction(Request $request)
    {
        $api = $this->get('symbb.core.api.forum');
        $groupApi = $this->get('symbb.core.api.user.group');
        $api->entityAccessCheck = false;
        $groupApi->entityAccessCheck = false;

        $data = $request->get('data');

        $forumFrom = $api->find($data['forumFrom']);
        $forumTo = $api->find($data['forumTo']);
        $group = $groupApi->find($data['group']);
        $childs = (bool)$data['childs'];

        $api->copyAccessOfGroup($forumFrom, $forumTo, $group, $childs);
        $api->addSuccessMessage(AbstractApi::SUCCESS_SAVED);
        return $api->getJsonResponse();
    }

    /**
     * @Route("/api/forum/applyAccessSet", name="symbb_backend_api_forum_apply_access_set")
     * @Method({"POST"})
     */
    public function applyAccessSetAction(Request $request)
    {
        $api = $this->get('symbb.core.api.forum');
        $groupApi = $this->get('symbb.core.api.user.group');
        $api->entityAccessCheck = false;
        $groupApi->entityAccessCheck = false;

        $data = $request->get('data');

        $forumTo = $api->find($data['forumTo']);
        $group = $groupApi->find($data['group']);
        $set = $data['set'];
        $childs = (bool)$data['childs'];
        $set = "default_" . $set;

        $api->applyAccessSetForGroup($forumTo, $group, $set, $childs);
        $api->addSuccessMessage(AbstractApi::SUCCESS_SAVED);
        return $api->getJsonResponse();
    }

}