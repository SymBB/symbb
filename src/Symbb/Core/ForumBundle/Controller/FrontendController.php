<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Controller;


use Symbb\Core\ForumBundle\Entity\Forum;

class FrontendController extends \Symbb\Core\SystemBundle\Controller\AbstractController
{

    public function indexAction()
    {
        $forum = new Forum();
        $topics = array();
        return $this->render($this->getTemplateBundleName('forum') . ':Forum:index.html.twig', array("forum" => $forum, "topics" => $topics));
    }

    public function showForumAction($id)
    {
        $forum = $this->get("symbb.core.forum.manager")->find($id);
        $topics = array();
        return $this->render($this->getTemplateBundleName('forum') . ':Forum:index.html.twig', array("forum" => $forum, "topics" => $topics));
    }

}