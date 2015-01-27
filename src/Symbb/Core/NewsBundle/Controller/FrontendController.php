<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\NewsBundle\Controller;


use Symbb\Core\ForumBundle\DependencyInjection\PostFlagHandler;
use Symbb\Core\ForumBundle\DependencyInjection\TopicFlagHandler;
use Symbb\Core\ForumBundle\Entity\Forum;
use Symbb\Core\ForumBundle\Entity\Post;
use Symbb\Core\ForumBundle\Entity\Topic;
use Symbb\Core\ForumBundle\Event\PostFormSaveEvent;
use Symbb\Core\ForumBundle\Event\TopicFormSaveEvent;
use Symbb\Core\ForumBundle\Form\TopicType;
use Symbb\Core\ForumBundle\Security\Authorization\ForumVoter;
use Symbb\Core\ForumBundle\Security\Authorization\PostVoter;
use Symbb\Core\ForumBundle\Security\Authorization\TopicVoter;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FrontendController
 * @package Symbb\Core\ForumBundle\Controller
 */
class FrontendController extends \Symbb\Core\ForumBundle\Controller\FrontendController
{

    /**
     * @param Request $request
     * @return mixed
     */
    public function createNewsAction(Request $request)
    {
        $entryId = $request->get("id");
        $entry = $this->get('symbb.core.manager.news')->find($entryId);

        if (is_object($entry)) {

            $forum = $entry->getCategory()->getTargetForum();

            if (!$this->get('security.authorization_checker')->isGranted(ForumVoter::CREATE_POST, $forum)) {
                throw $this->createAccessDeniedException();
            }

            $topic = new Topic();
            $topic->setForum($forum);
            $topic->setAuthor($this->getUser());
            $topic->setName($entry->getTitle());
            $entry->setTopic($topic);
            $this->get("doctrine.orm.symbb_entity_manager")->persist($entry);

            $post = new Post();
            $post->setAuthor($this->getUser());
            $post->setText($entry->getText());
            $post->setTopic($topic);
            $post->setName($entry->getTitle());

            $topic->setMainPost($post);

            return $this->handleTopic($request, $topic);

        }

        throw new \ErrorException("News not found!");
    }

}