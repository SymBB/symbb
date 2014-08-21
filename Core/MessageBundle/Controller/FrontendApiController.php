<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\MessageBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use SymBB\Core\ForumBundle\Entity\Forum;
use SymBB\Core\ForumBundle\Entity\Post\History;
use Symfony\Component\HttpFoundation\Request;

class FrontendApiController extends \SymBB\Core\SystemBundle\Controller\AbstractApiController
{

    const ERROR_NOT_FOUND_FORUM = 'forum not found';
    const ERROR_NOT_FOUND_TOPIC = 'topic not found';
    const ERROR_NOT_FOUND_POST = 'post not found';
    const ERROR_NOT_FOUND_FILE = 'file not found';
    const ERROR_ACCESS_DELETE_POST = 'access denied (delete post)';
    const ERROR_ACCESS_EDIT_POST = 'access denied (edit post)';
    const ERROR_ACCESS_CREATE_POST = 'access denied (create post)';
    const ERROR_ACCESS_VIEW_FORUM = 'access denied (show forum)';
    const ERROR_ACCESS_EDIT_TOPIC = 'access denied (edit topic)';
    const ERROR_ACCESS_CREATE_TOPIC = 'access denied (create topic)';


    /**
     * @Route("/api/message/list", name="symbb_api_message_list")
     * @Method({"GET"})
     */
    public function searchPostsAction(Request $request)
    {

        $params['entries'] = array();
        $posts = $this->get('symbb.core.post.manager')->search($request);
        $breadcrumb = $this->get('symbb.core.forum.manager')->getBreadcrumbData();
        $this->addBreadcrumbItems($breadcrumb);
        $this->addPaginationData($posts);
        foreach ($posts as $post) {
            $params['entries'][] = $this->getPostAsArray($post);
        }
        $params['count']['post'] = $this->paginationData['totalCount'];
        return $this->getJsonResponse($params);
    }
}