<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\NewsBundle\Controller;

use Symbb\Core\SystemBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class BackendApiController extends AbstractController
{

    /**
     * @Route("/api/news/category", name="symbb_backend_api_news_category_list")
     * @Method({"GET"})
     */
    public function listAction()
    {
        $api = $this->get('symbb.core.api.news.category');
        $objects = $api->getList();

        $objectsData = array();
        foreach ($objects as $object) {
            $objectsData[] = $api->createArrayOfObject($object);
        }

        $forumList = $this->getForumTreeArray();
        return $api->getJsonResponse(array(
            'data' => $objectsData,
            'forumList' => $forumList
        ));
    }

    /**
     * @Route("/api/news/category", name="symbb_backend_api_news_category_save")
     * @Method({"POST"})
     */
    public function saveAction(Request $request)
    {
        $api = $this->get('symbb.core.api.news.category');
        $data = $request->get('data');
        $site = $api->save($data);
        if(is_object($site)){
            $site = $api->createArrayOfObject($site);
        }
        return $api->getJsonResponse(array(
            'data' => $site
        ));
    }

    /**
     * @Route("/api/news/category/{category}", name="symbb_backend_api_news_category_data")
     * @Method({"GET"})
     */
    public function findAction($category)
    {
        $api = $this->get('symbb.core.api.news.category');
        $api->find((int)$category);
        return $api->getJsonResponse();
    }

    /**
     * @Route("/api/news/category/{category}", name="symbb_backend_api_news_category_delete")
     * @Method({"DELETE"})
     */
    public function deleteAction($category)
    {
        $api = $this->get('symbb.core.api.news.category');
        $api->delete((int)$category);
        return $api->getJsonResponse();
    }


    /**
     * @Route("/api/news", name="symbb_backend_api_news_list")
     * @Method({"GET"})
     */
    public function newsAction()
    {
        $api = $this->get('symbb.core.api.news');

        $objects = $api->getList();
        $objectsData = array();
        foreach ($objects as $object) {
            $objectsData[] = $api->createArrayOfObject($object);
        }

        $newNews = $api->collectNews();

        return $api->getJsonResponse(array(
            'oldNews' => $objectsData,
            'newNews' => $newNews
        ));
    }

}