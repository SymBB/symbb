<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\ForumBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class FrontendApiController extends \SymBB\Core\SystemBundle\Controller\AbstractApiController
{

    /**
     * @Route("/api/topic/{id}/upload/image", name="symbb_api_forum_topic_upload_image")
     * @Method({"POST"})
     */
    public function topicUploadImageAction($id)
    {
        $params = array();

        $files = $this->get('request')->files;
        if (\is_object($files)) {
            $uploadManager = $this->get('symbb.core.upload.manager');
            $uploadSet = 'tmp';
            if ($id > 0) {
                $uploadSet = 'post';
            }
            $params['files'] = $uploadManager->handleUpload($this->get('request'), $uploadSet);
        } else {

            $this->addErrorMessage("Image not found");
        }

        return $this->getJsonResponse($params);
    }

    /**
     * @Route("/api/forum/{forumId}/topic/save", name="symbb_api_forum_topic_save")
     * @Method({"POST"})
     */
    public function topicSaveAction($forumId)
    {

        $request = $this->get('request');
        $topicId = $request->get('id');

        $params = array();

        $forum = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')->find($forumId);

        if (is_object($forum)) {

            $this->get('symbb.core.access.manager')->addAccessCheck('SYMBB_FORUM#CREATE_TOPIC', $forum, $this->getUser());
            $writeAccess = $this->get('symbb.core.access.manager')->hasAccess();

            if ($writeAccess) {

                if ($topicId > 0) {
                    $topic = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Topic', 'symbb')->find($topicId);
                    $mainPost = $topic->getMainPost();
                    $this->get('symbb.core.access.manager')->addAccessCheck('SYMBB_TOPIC#EDIT', $topic, $this->getUser());
                    $editAccess = $this->get('symbb.core.access.manager')->hasAccess();
                } else {
                    $topic = new \SymBB\Core\ForumBundle\Entity\Topic();
                    $topic->setAuthor($this->getUser());
                    $mainPost = new \SymBB\Core\ForumBundle\Entity\Post();
                    $mainPost->setTopic($topic);
                    $mainPost->setAuthor($this->getUser());
                    $topic->setMainPost($mainPost);
                    $topic->setForum($forum);
                    $editAccess = true;
                }

                if ($editAccess) {

                    $em = $this->getDoctrine()->getManager('symbb');

                    $mainPostData = $request->get('mainPost');

                    $topic->setName($request->get('name'));
                    $topic->setLocked($request->get('locked'));
                    $mainPost->setText($mainPostData['text']);

                    if (isset($mainPostData['files'])) {
                        $currentFiles = $mainPost->getFiles();
                        // check for new stuff
                        foreach ($mainPostData['files'] as $file) {
                            $found = false;
                            foreach ($currentFiles as $currentFile) {
                                if ($currentFile->getPath() == $file) {
                                    $found = true;
                                }
                            }
                            if (!$found) {
                                $newFile = new \SymBB\Core\ForumBundle\Entity\Post\File();
                                $newFile->setPath($file);
                                $newFile->setPost($mainPost);
                                $mainPost->addFile($newFile);
                            }
                        }
                        // check for old stuff
                        foreach ($currentFiles as $currentFile) {
                            $found = false;
                            foreach ($mainPostData['files'] as $file) {
                                if ($currentFile->getPath() == $file) {
                                    $found = true;
                                }
                            }
                            if (!$found) {
                                $em->remove($currentFile);
                                $mainPost->removeFile($currentFile);
                            }
                        }
                    }



                    $em->persist($topic);
                    $em->persist($mainPost);
                    $em->flush();

                    if ($request->get('notifyMe')) {
                        $this->get('symbb.core.topic.flag')->checkFlag($topic, 'notify');
                    } else {
                        $this->get('symbb.core.topic.flag')->removeFlag($topic, 'notify');
                    }

                    $this->addSuccessMessage('saved successfully.');

                    $params['id'] = $topic->getId();
                } else {
                    $this->addErrorMessage('access denied (edit topic)');
                }
            } else {
                $this->addErrorMessage('access denied (create topic)');
            }
        } else {
            $this->addErrorMessage('forum not found');
        }

        return $this->getJsonResponse($params);
    }

    /**
     * @Route("/api/forum/{id}/topic/create", name="symbb_api_forum_topic_create")
     * @Method({"GET"})
     */
    public function topicCreateAction($id)
    {
        $forum = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')->find($id);
        $params = array();
        $params['forum'] = $this->getForumAsArray($forum);
        $params['topic'] = $this->getTopicAsArray();
        $breadcrumbItems = $this->get('symbb.core.forum.manager')->getBreadcrumbData($forum);
        $this->addBreadcrumbItems($breadcrumbItems);
        return $this->getJsonResponse($params);
    }

    /**
     * @Route("/api/forum/{id}/ignore", name="symbb_api_forum_ignore")
     * @Method({"POST"})
     */
    public function forumIgnore($id)
    {

        $forum = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')
            ->find($id);
        $this->get('symbb.core.forum.manager')->ignoreForum($forum, $this->get('symbb.core.forum.flag'));

        $this->addSuccessMessage('You are now ignoring the Forum');
        $this->addCallback('refesh');

        return $this->getJsonResponse(array());
    }

    /**
     * @Route("/api/forum/{id}/unignore", name="symbb_api_forum_unignore")
     * @Method({"POST"})
     */
    public function forumUnignore($id)
    {

        $forum = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')
            ->find($id);

        $this->get('symbb.core.forum.manager')->watchForum($forum, $this->get('symbb.core.forum.flag'));

        $this->addSuccessMessage('You are now watching the forum again');
        $this->addCallback('refesh');

        return $this->getJsonResponse(array());
    }

    /**
     * @Route("/api/forum/{id}/markAsRead", name="symbb_api_forum_mark_as_read")
     * @Method({"POST"})
     */
    public function forumMarkAsRead($id)
    {
        $forum = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')
            ->find($id);
        $this->get('symbb.core.forum.manager')->markAsRead($forum, $this->get('symbb.core.forum.flag'));

        $this->addSuccessMessage('The forum has been marked as read');
        $this->addCallback('refesh');

        return $this->getJsonResponse(array());
    }

    /**
     * @Route("/api/topic/{id}/posts/{page}", name="symbb_api_forum_topic_post_list")
     * @Method({"GET"})
     */
    public function topicPostListAction($id, $page)
    {
        $topic = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Topic', 'symbb')
            ->find($id);

        $lastPosts = $this->get('symbb.core.topic.manager')->findPosts($topic, $page);

        $params = array('items' => array(), 'total' => count($lastPosts));
        foreach ($lastPosts as $post) {
            $params['items'][] = $this->getPostAsArray($post);
        }

        return $this->getJsonResponse($params);
    }

    /**
     * @Route("/api/topic/{id}/data", name="symbb_api_forum_topic_data")
     * @Method({"GET"})
     */
    public function topicShowAction($id)
    {
        $topic = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Topic', 'symbb')
            ->find($id);

        $breadcrumbItems = $this->get('symbb.core.topic.manager')->getBreadcrumbData($topic, $this->get('symbb.core.forum.manager'));
        $this->addBreadcrumbItems($breadcrumbItems);

        $this->get('symbb.core.access.manager')->addAccessCheck('SYMBB_FORUM#CREATE_POST', $topic->getForum(), $this->getUser());
        $writeAccess = $this->get('symbb.core.access.manager')->hasAccess();

        $this->get('symbb.core.access.manager')->addAccessCheck('SYMBB_TOPIC#EDIT', $topic, $this->getUser());
        $editAccess = $this->get('symbb.core.access.manager')->hasAccess();

        $this->get('symbb.core.access.manager')->addAccessCheck('SYMBB_TOPIC#DELETE', $topic, $this->getUser());
        $deleteAccess = $this->get('symbb.core.access.manager')->hasAccess();

        $params = array(
            'topic' => $this->getTopicAsArray($topic),
            'access' => array(
                'create' => $writeAccess,
                'edit' => $editAccess,
                'delete' => $deleteAccess
            )
        );

        return $this->getJsonResponse($params);
    }

    /**
     * @Route("/api/forum/{id}/topics/{page}", name="symbb_api_forum_topic_list")
     * @Method({"GET"})
     */
    public function forumTopicListAction($id, $page)
    {

        $forum = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')
            ->find($id);

        $topics = $this->get('symbb.core.forum.manager')->findTopics($forum, $page);

        $params = array('items' => array(), 'total' => count($topics));
        foreach ($topics as $topic) {
            $params['items'][] = $this->getTopicAsArray($topic);
        }

        return $this->getJsonResponse($params);
    }

    /**
     * @Route("/api/forum/{id}/data", name="symbb_api_forum_data", defaults={"id" = 0})
     * @Method({"GET"})
     */
    public function forumDataAction($id)
    {

        if ((int) $id === 0) {
            $id = null;
        }

        $forumEnityList = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')
            ->findBy(array('parent' => $id, 'type' => 'forum'), array('position' => 'asc', 'id' => 'asc'));

        $forumList = array();

        $hasForumList = false;
        foreach ($forumEnityList as $key => $forum) {
            $this->get('symbb.core.access.manager')->addAccessCheck('SYMBB_FORUM#VIEW', $forum, $this->getUser());
            $access = $this->get('symbb.core.access.manager')->hasAccess();
            if ($access) {
                $forumList[] = $this->getForumAsArray($forum);
                $hasForumList = true;
            };
        }

        $categoryEnityList = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')
            ->findBy(array('parent' => $id, 'type' => 'category'), array('position' => 'asc', 'id' => 'asc'));

        $categoryList = array();
        $hasCategoryList = false;
        foreach ($categoryEnityList as $key => $forum) {
            $this->get('symbb.core.access.manager')->addAccessCheck('SYMBB_FORUM#VIEW', $forum, $this->getUser());
            $access = $this->get('symbb.core.access.manager')->hasAccess();
            if ($access) {
                $categoryList[] = $this->getForumAsArray($forum);
                $hasCategoryList = true;
            };
        }


        $topicList = array();
        $topicCountTotal = 0;
        $hasTopicList = false;
        $parent = null;
        if ($id > 0) {
            $parent = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')->find($id);
            $topics = $this->get('symbb.core.forum.manager')->findTopics($parent);
            $topicCountTotal = count($topics);
            foreach ($topics as $topic) {
                $topicList[] = $this->getTopicAsArray($topic);
                $hasTopicList = true;
            }
        }


        $breadcrumbItems = $this->get('symbb.core.forum.manager')->getBreadcrumbData($parent);
        $this->addBreadcrumbItems($breadcrumbItems);

        $params = array(
            'forum' => $this->getForumAsArray($parent),
            'categoryList' => $categoryList,
            'forumList' => $forumList,
            'topicList' => $topicList,
            'topicTotalCount' => $topicCountTotal,
            'hasForumList' => $hasForumList,
            'hasCategoryList' => $hasCategoryList,
            'hasTopicList' => $hasTopicList
        );

        return $this->getJsonResponse($params);
    }

    /**
     * 
     * @param \SymBB\Core\ForumBundle\Entity\Topic $topic
     * @return array
     */
    protected function getTopicAsArray(\SymBB\Core\ForumBundle\Entity\Topic $topic = null)
    {
        $array = array();
        $array['id'] = 0;
        $array['name'] = '';
        $array['locked'] = false;
        $array['notifyMe'] = false;
        $array['count']['post'] = 0;
        $array['backgroundImage'] = '';
        $array['flags'] = array();
        $array['posts'] = array();
        $array['seo']['name'] = '';
        $array['access'] = array(
            'createPost' => false,
            'edit' => false,
            'delete' => false
        );

        if (is_object($topic)) {
            $array['id'] = $topic->getId();
            $array['name'] = $topic->getName();
            $array['locked'] = $topic->isLocked();
            $array['backgroundImage'] = $this->get('symbb.core.user.manager')->getAvatar($topic->getAuthor());
            foreach ($this->get('symbb.core.topic.flag')->findAll($topic) as $flag) {
                $array['flags'][$flag->getFlag()] = $this->getFlagAsArray($flag);
            }
            $posts = $this->get('symbb.core.topic.manager')->findPosts($topic);
            $array['count']['post'] = count($posts);
            foreach ($posts as $post) {
                $array['posts'][] = $this->getPostAsArray($post);
            }
            $array['seo']['name'] = $topic->getSeoName();

            $array['notifyMe'] = $this->get('symbb.core.topic.flag')->checkFlag($topic, 'notify');

            $array['mainPost'] = $this->getPostAsArray($topic->getMainPost());
            $this->get('symbb.core.access.manager')->addAccessCheck('SYMBB_FORUM#CREATE_POST', $topic->getForum(), $this->getUser());
            $writePostAccess = $this->get('symbb.core.access.manager')->hasAccess();

            $this->get('symbb.core.access.manager')->addAccessCheck('SYMBB_TOPIC#EDIT', $topic, $this->getUser());
            $editAccess = $this->get('symbb.core.access.manager')->hasAccess();

            $this->get('symbb.core.access.manager')->addAccessCheck('SYMBB_TOPIC#DELETE', $topic, $this->getUser());
            $deleteAccess = $this->get('symbb.core.access.manager')->hasAccess();

            $array['access'] = array(
                'createPost' => $writePostAccess,
                'edit' => $editAccess,
                'delete' => $deleteAccess
            );
        } else {
            $array['mainPost'] = $this->getPostAsArray();
        }
        return $array;
    }

    /**
     * 
     * @param \SymBB\Core\ForumBundle\Entity\Forum $forum
     * @return type
     */
    protected function getForumAsArray(\SymBB\Core\ForumBundle\Entity\Forum $forum = null)
    {

        $array = array();
        $array['id'] = 0;
        $array['name'] = '';
        $array['description'] = '';
        $array['ignore'] = false;
        $array['count']['topic'] = 0;
        $array['count']['post'] = 0;
        $array['backgroundImage'] = "";
        $array['flags'] = array();
        $array['children'] = array();
        $array['lastPosts'] = array();
        $array['seo']['name'] = '';
        $array['access'] = array(
            'createTopic' => false,
            'createPost' => false
        );

        if (is_object($forum)) {
            $array['id'] = $forum->getId();
            $array['name'] = $forum->getName();
            $array['description'] = $forum->getDescription();
            $array['count']['topic'] = $forum->getTopicCount();
            $array['count']['post'] = $forum->getPostCount();

            foreach ($this->get('symbb.core.forum.flag')->findAll($forum) as $flag) {
                $array['flags'][$flag->getFlag()] = $this->getFlagAsArray($flag);
            }
            foreach ($forum->getChildren() as $child) {
                $array['children'][] = $this->getForumAsArray($child);
            }
            $lastPosts = $this->get('symbb.core.forum.manager')->findPosts($forum, 10);
            foreach ($lastPosts as $post) {
                $array['lastPosts'][] = $this->getPostAsArray($post);
            }
            $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
            if ($forum->getImageName()) {
                $array['backgroundImage'] = $helper->asset($forum, 'image');
            }
            $array['seo']['name'] = $forum->getSeoName();

            $this->get('symbb.core.access.manager')->addAccessCheck('SYMBB_FORUM#CREATE_TOPIC', $forum, $this->getUser());
            $writeAccess = $this->get('symbb.core.access.manager')->hasAccess();

            $this->get('symbb.core.access.manager')->addAccessCheck('SYMBB_FORUM#CREATE_POST', $forum, $this->getUser());
            $writePostAccess = $this->get('symbb.core.access.manager')->hasAccess();

            $array['access'] = array(
                'createTopic' => $writeAccess,
                'createPost' => $writePostAccess
            );

            $array['ignored'] = $this->get('symbb.core.forum.manager')->isIgnored($forum, $this->get('symbb.core.forum.flag'));
        }

        return $array;
    }

    /**
     * 
     * @param \SymBB\Core\ForumBundle\Entity\Forum\Flag $flag
     * @return array
     */
    protected function getFlagAsArray($flag)
    {
        $array = array();
        $flagName = $flag->getFlag();
        if ($flagName == 'new') {
            $array['type'] = 'success';
        } else if ($flagName == 'answered') {
            $array['type'] = 'warning';
        } else {
            $array['type'] = $flagName;
        }
        $array['title'] = $this->get('translator')->trans($flagName, array(), 'symbb_frontend');
        return $array;
    }

    /**
     * 
     * @param \SymBB\Core\ForumBundle\Entity\Post $post
     * @return array
     */
    protected function getPostAsArray(\SymBB\Core\ForumBundle\Entity\Post $post = null)
    {

        $array = array();
        $array['id'] = 0;
        $array['topic']['id'] = 0;
        $array['name'] = '';
        $array['changed'] = 0;
        $array['text'] = '';
        $array['rawText'] = '';
        $array['signature'] = '';
        $array['files'] = array();
        $array['seo']['name'] = '';
        $array['access']['edit'] = false;
        $array['access']['delete'] = false;

        if (is_object($post)) {
            $array['id'] = $post->getId();
            $array['topic']['id'] = $post->getTopic()->getId();
            $array['name'] = $post->getName();
            $array['changed'] = $this->getCorrectTimestamp($post->getChanged());
            $array['author'] = $this->getAuthorAsArray($post->getAuthor());
            $array['seo']['name'] = $post->getSeoName();
            $array['rawText'] = $post->getText();
            $array['text'] = $this->get('symbb.core.post.manager')->parseText($post);
            $array['signature'] = $this->get('symbb.core.user.manager')->getSignature($post->getAuthor());

            foreach ($post->getFiles() as $file) {
                $array['files'][] = $file->getPath();
            }

            $this->get('symbb.core.access.manager')->addAccessCheck('SYMBB_POST#EDIT', $post, $this->getUser());
            $editAccess = $this->get('symbb.core.access.manager')->hasAccess();

            $this->get('symbb.core.access.manager')->addAccessCheck('SYMBB_POST#DELETE', $post, $this->getUser());
            $deleteAccess = $this->get('symbb.core.access.manager')->hasAccess();

            $array['access'] = array(
                'edit' => $editAccess,
                'delete' => $deleteAccess
            );
        } else {
            $array['author'] = $this->getAuthorAsArray();
        }

        return $array;
    }

    /**
     * 
     * @param \SymBB\Core\UserBundle\Entity\UserInterface $author
     * @return array
     */
    protected function getAuthorAsArray(\SymBB\Core\UserBundle\Entity\UserInterface $author = null)
    {
        $array = array();
        $array['id'] = 0;
        $array['username'] = '';
        $array['avatar'] = '';
        $array['count']['topic'] = 0;
        $array['count']['post'] = 0;
        $array['created'] = 0;

        if (is_object($author)) {
            $array['id'] = $author->getId();
            $array['username'] = $author->getUsername();
            $array['avatar'] = $this->get('symbb.core.user.manager')->getAbsoluteAvatarUrl($author);
            $array['count']['topic'] = $this->get('symbb.core.user.manager')->getTopicCount($author);
            $array['count']['post'] = $this->get('symbb.core.user.manager')->getPostCount($author);
            $array['created'] = $this->getCorrectTimestamp($author->getCreated());
        }

        return $array;
    }
}