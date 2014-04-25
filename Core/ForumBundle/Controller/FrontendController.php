<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\ForumBundle\Controller;

class FrontendController extends \SymBB\Core\SystemBundle\Controller\AbstractController
{

    public function indexAction()
    {
        return $this->portalAction();
    }

    public function portalAction()
    {
        return $this->render($this->getTemplateBundleName('forum') . ':Forum:index.html.twig', array());
    }

    public function forumAction()
    {
        return $this->render($this->getTemplateBundleName('forum') . ':Forum:index.html.twig', array());
    }

    public function newestAction()
    {

        $posts = $this->get('symbb.core.forum.manager')->findNewestPosts(null, 50);

        return $this->render($this->getTemplateBundleName('forum') . ':Forum:newest.html.twig', array('posts' => $posts));

    }

    public function ignoreAction($forum)
    {

        $forum = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')
            ->find($forum);

        if (is_object($forum)) {
            $this->ignoreForum($forum);
        }

        return $this->forward('SymBBCoreForumBundle:Frontend:forumShow', array(
                'name' => $forum->getSeoName(),
                'id' => $forum->getId()
        ));

    }

    public function watchAction($forum)
    {
        $forum = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')
            ->find($forum);
        if (is_object($forum)) {
            $this->watchForum($forum);
        }
        return $this->forward('SymBBCoreForumBundle:Frontend:forumShow', array(
                'name' => $forum->getSeoName(),
                'id' => $forum->getId()
        ));

    }

    public function readAction($forum)
    {
        $forum = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')
            ->find($forum);
        if (is_object($forum)) {
            $this->readForum($forum);
        }
        return $this->forward('SymBBCoreForumBundle:Frontend:forumShow', array(
                'name' => $forum->getSeoName(),
                'id' => $forum->getId()
        ));

    }

    protected function ignoreForum(\SymBB\Core\ForumBundle\Entity\Forum $forum)
    {
        $flagHandler = $this->get('symbb.core.forum.flag');
        $flagHandler->insertFlag($forum, 'ignore');
        $subForms = $forum->getChildren();
        foreach ($subForms as $subForm) {
            $this->ignoreForum($subForm);
        }

    }

    protected function watchForum(\SymBB\Core\ForumBundle\Entity\Forum $forum)
    {
        $flagHandler = $this->get('symbb.core.forum.flag');
        $flagHandler->removeFlag($forum, 'ignore');
        $subForms = $forum->getChildren();
        foreach ($subForms as $subForm) {
            $this->watchForum($subForm);
        }

    }

    protected function readForum(\SymBB\Core\ForumBundle\Entity\Forum $forum)
    {

        $topics = $forum->getTopics();
        foreach ($topics as $topic) {
            $flagHandler = $this->get('symbb.core.topic.flag');
            $flagHandler->removeFlag($topic, 'new');
        }

        $subForms = $forum->getChildren();
        foreach ($subForms as $subForm) {
            $this->readAction($subForm);
        }

    }
}