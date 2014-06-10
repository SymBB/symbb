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
use SymBB\Core\ForumBundle\Entity\Forum;

class FrontendApiController extends \SymBB\Core\SystemBundle\Controller\AbstractApiController
{

    /**
     * @Route("/api/post/search", name="symbb_api_post_search")
     * @Method({"GET"})
     */
    public function searchPostsAction()
    {

        $params['entries'] = array();
        $posts = $this->get('symbb.core.post.manager')->search($this->get('request'));
        $breadcrumb = $this->get('symbb.core.forum.manager')->getBreadcrumbData();
        $this->addBreadcrumbItems($breadcrumb);
        $this->addPaginationData($posts);
        foreach ($posts as $post) {
            $params['entries'][] = $this->getPostAsArray($post);
        }
        $params['count']['post'] = $this->paginationData['totalCount'];
        return $this->getJsonResponse($params);
    }

    /**
     * @Route("/api/post/upload/image", name="symbb_api_post_upload_image")
     * @Method({"POST"})
     */
    public function postUploadImageAction()
    {

        $id = (int) $this->get('request')->get('id');

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
     * @Route("/api/post/delete", name="symbb_api_post_delete")
     * @Method({"DELETE"})
     */
    public function postDeleteAction()
    {
        $id = (int) $this->get('request')->get('id');
        $params = array();

        if ($id > 0) {
            $post = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Post', 'symbb')->find($id);
            $accessCheck = $this->get('security.context')->isGranted('DELETE', $post);
            if (!$accessCheck) {
                $this->addErrorMessage('access denied (delete post)');
            } else {

                $em = $this->getDoctrine()->getManager('symbb');

                $event = new \SymBB\Core\EventBundle\Event\ApiDeleteEvent($post);
                $this->handleEvent('symbb.api.post.before.delete', $event);

                if (!$this->hasError()) {
                    $topic = $post->getTopic();
                    if ($topic->getMainPost()->getId() === $post->getId()) {
                        foreach ($topic->getPosts() as $currPost) {
                            $em->remove($currPost);
                        }
                        $em->remove($topic);
                    } else {
                        $em->remove($post);
                    }

                    $em->flush();
                }

                $event = new \SymBB\Core\EventBundle\Event\ApiDeleteEvent($post);
                $this->handleEvent('symbb.api.post.after.delete', $event);

                $this->addSuccessMessage('successfully deleted');
                $this->addCallback('refresh');
            }
        } else {
            $this->addErrorMessage("Post not found");
        }

        return $this->getJsonResponse($params);
    }

    protected function handleEvent($eventName, $eventObject)
    {
        $this->get('event_dispatcher')->dispatch($eventName, $eventObject);
        $messages = (array) $eventObject->getMessages();
        $callbacks = (array) $eventObject->getCallbacks();
        $this->messages = array_merge($this->messages, $messages);
        $this->callbacks = array_merge($this->callbacks, $callbacks);
    }

    /**
     * @Route("/api/post/save", name="symbb_api_post_save")
     * @Method({"POST"})
     */
    public function postSaveAction()
    {
        $request = $this->get('request');
        $id = (int) $request->get('id');
        $topicData = $request->get('topic');
        $params = array();
        $accessCheck = false;

        if ($id > 0) {
            $post = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Post', 'symbb')->find($id);
            $accessCheck = $this->get('security.context')->isGranted('EDIT', $post);
            if (!$accessCheck) {
                $this->addErrorMessage('access denied (edit post)');
            }
            $topic = $post->getTopic();
        } else {
            $post = new \SymBB\Core\ForumBundle\Entity\Post();
            if (isset($topicData['id']) && $topicData['id'] > 0) {
                $topic = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Topic', 'symbb')->find($topicData['id']);
                $post->setTopic($topic);
                $accessCheck = $this->get('security.context')->isGranted('CREATE_POST', $topic->getForum());
                if (!$accessCheck) {
                    $this->addErrorMessage('access denied (create post)');
                }
            } else {
                $this->addErrorMessage("Topic not found!");
            }
        }

        if (!$this->hasError()) {


            $em = $this->getDoctrine()->getManager('symbb');

            $post->setName($request->get('name'));
            $post->setText($request->get('rawText'));
            if ($id <= 0) {
                $post->setAuthor($this->getUser());
            }

            $this->handlePostImages($post, (array) $request->get('files'), $em);

            $event = new \SymBB\Core\EventBundle\Event\ApiSaveEvent($post, (array) $this->get('request')->get('extension'));
            $this->handleEvent('symbb.api.post.before.save', $event);

            if (!$this->hasError()) {
                $em->persist($post);
                $em->flush();
            }

            $event = new \SymBB\Core\EventBundle\Event\ApiSaveEvent($post, (array) $this->get('request')->get('extension'));
            $this->handleEvent('symbb.api.post.after.save', $event);

            $params['id'] = $post->getId();
            if ($request->get('notifyMe')) {
                $this->get('symbb.core.topic.flag')->insertFlag($post->getTopic(), 'notify');
            } else {
                $this->get('symbb.core.topic.flag')->removeFlag($post->getTopic(), 'notify');
            }

            $this->get('symbb.core.post.flag')->insertFlags($post, 'new');
        }

        return $this->getJsonResponse($params);
    }

    /**
     * @Route("/api/post/data", name="symbb_api_post_data")
     * @Method({"GET"})
     */
    public function postDataAction()
    {
        $id = (int) $this->get('request')->get('id');
        $topicId = (int) $this->get('request')->get('topic');
        $quoteId = (int) $this->get('request')->get('quoteId');
        $params = array();

        $post = new \SymBB\Core\ForumBundle\Entity\Post();
        if ($id > 0) {
            $post = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Post', 'symbb')->find($id);
            $accessCheck = $this->get('security.context')->isGranted('VIEW', $post->getTopic()->getForum());
            if (!$accessCheck) {
                $this->addErrorMessage('access denied (show forum)');
            }
        } else {
            if ($topicId > 0) {
                $topic = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Topic', 'symbb')->find($topicId);
                $post->setTopic($topic);
                $post->setName($topic->getName());
                if ($quoteId > 0) {
                    $qoutePost = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Post', 'symbb')->find($quoteId);
                    $post->setText('[quote="' . $qoutePost->getAuthor()->getUsername() . '"]' . $qoutePost->getText() . '[/quote]');
                }
                $accessCheck = $this->get('security.context')->isGranted('VIEW', $topic->getForum());
                if (!$accessCheck) {
                    $this->addErrorMessage('access denied (show forum)');
                }
            } else {
                $this->addErrorMessage("Topic not found!");
            }
        }

        if (!$this->hasError()) {
            $params['post'] = $this->getPostAsArray($post);

            $breadcrumbItems = $this->get('symbb.core.post.manager')->getBreadcrumbData($post, $this->get('symbb.core.topic.manager'), $this->get('symbb.core.forum.manager'));
            $this->addBreadcrumbItems($breadcrumbItems);
        }

        return $this->getJsonResponse($params);
    }
    
    /**
     * @Route("/api/topic/save", name="symbb_api_topic_save")
     * @Method({"POST"})
     */
    public function topicSaveAction()
    {

        $request = $this->get('request');
        $topicId = (int) $request->get('id');
        $forumData = (array) $request->get('forum');

        $params = array();
            
        if (isset($forumData['id'])) {

            $forum = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')->find($forumData['id']);

            if (is_object($forum)) {
                $writeAccess = $this->get('security.context')->isGranted('CREATE_TOPIC', $forum);

                if ($writeAccess) {

                    if ($topicId > 0) {
                        $topic = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Topic', 'symbb')->find($topicId);
                        $mainPost = $topic->getMainPost();
                        $editAccess = $this->get('security.context')->isGranted('EDIT', $topic);
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
                        $mainPost->setText($mainPostData['rawText']);

                        $topic->removeTags();
                        foreach($request->get('tags') as $tagId => $tag){
                            if($tag['status'] === true){
                                $myTag = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Topic\tag', 'symbb')->find($tag['id']);
                                if(is_object($myTag)){
                                    $topic->addTag($myTag);
                                }
                            }
                        }

                        $this->handlePostImages($mainPost, $mainPostData['files'], $em);

                        $event = new \SymBB\Core\EventBundle\Event\ApiSaveEvent($topic, (array) $this->get('request')->get('extension'));
                        $this->handleEvent('symbb.api.topic.before.save', $event);

                        if (!$this->hasError()) {
                            $em->persist($topic);
                            $em->persist($mainPost);
                            $em->flush();

                            if ($request->get('notifyMe')) {
                                $this->get('symbb.core.topic.flag')->insertFlag($topic, 'notify');
                            } else {
                                $this->get('symbb.core.topic.flag')->removeFlag($topic, 'notify');
                            }

                            $this->get('symbb.core.post.flag')->insertFlags($mainPost, 'new');
                            if ($topic->isLocked()) {
                                $this->get('symbb.core.topic.flag')->insertFlags($topic, 'locked');
                            }
                            $this->addSuccessMessage('saved successfully.');

                            $params['id'] = $topic->getId();
                        }

                        $event = new \SymBB\Core\EventBundle\Event\ApiSaveEvent($topic, (array) $this->get('request')->get('extension'));
                        $this->handleEvent('symbb.api.topic.after.save', $event);
                    } else {
                        $this->addErrorMessage('access denied (edit topic)');
                    }
                } else {
                    $this->addErrorMessage('access denied (create topic)');
                }
            } else {
                $this->addErrorMessage('forum not found');
            }
        } else {
            $this->addErrorMessage("Forum not found!");
        }

        return $this->getJsonResponse($params);
    }

    /**
     * @Route("/api/forum/topic/list", name="symbb_api_forum_topic_list")
     * @Method({"GET"})
     */
    public function forumTopicListAction()
    {
        $list = array();
        $forumId = (int) $this->get('request')->get('forum');
        $page = $this->get('request')->get('page');
        if($forumId > 0){
            $forum = $this->get('symbb.core.forum.manager')->find($forumId);
            $accessCheck = $this->get('security.context')->isGranted('VIEW', $forum);
            if (!$accessCheck) {
                $this->addErrorMessage('access denied (show forum)');
            }

            if (!$this->hasError()) {
                $topics = $this->get('symbb.core.forum.manager')->findTopics($forum, $page);
                $this->addPaginationData($topics);
                foreach($topics as $topic){
                    $list[] = $this->getTopicAsArray($topic);
                }
            }
        }
        return $this->getJsonResponse(array('topics' => $list));
    }

    /**
     * @Route("/api/topic/data", name="symbb_api_topic_data")
     * @Method({"GET"})
     */
    public function topicDataAction()
    {
        $id = (int) $this->get('request')->get('id');
        $forumId = (int) $this->get('request')->get('forum');
        $params = array();

        $topic = new \SymBB\Core\ForumBundle\Entity\Topic();
        if ($id > 0) {
            $topic = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Topic', 'symbb')->find($id);
        } else {
            if ($forumId > 0) {
                $forum = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')->find($forumId);
                $topic->setForum($forum);
            } else {
                $this->addErrorMessage("Forum not found!");
            }
        }

        $accessCheck = $this->get('security.context')->isGranted('VIEW', $topic->getForum());
        if (!$accessCheck) {
            $this->addErrorMessage('access denied (show forum)');
        }

        if (!$this->hasError()) {
            $page = $this->get('request')->get('page');
            $params['topic'] = $this->getTopicAsArray($topic, $page, null, true);
            $breadcrumbItems = $this->get('symbb.core.topic.manager')->getBreadcrumbData($topic, $this->get('symbb.core.forum.manager'));
            $this->addBreadcrumbItems($breadcrumbItems);
            $this->get('symbb.core.topic.flag')->removeFlag($topic, 'new');
        }

        return $this->getJsonResponse($params);
    }

    /**
     * @Route("/api/forum/ignore", name="symbb_api_forum_ignore")
     * @Method({"POST"})
     */
    public function forumIgnore()
    {

        $id = (int) $this->get('request')->get('id');

        $forum = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')
            ->find($id);
        $this->get('symbb.core.forum.manager')->ignoreForum($forum, $this->get('symbb.core.forum.flag'));

        $this->addSuccessMessage('You are now ignoring the Forum');
        $this->addCallback('refresh');

        return $this->getJsonResponse(array());
    }

    /**
     * @Route("/api/forum/unignore", name="symbb_api_forum_unignore")
     * @Method({"POST"})
     */
    public function forumUnignore()
    {

        $id = (int) $this->get('request')->get('id');

        $forum = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')
            ->find($id);

        $this->get('symbb.core.forum.manager')->watchForum($forum, $this->get('symbb.core.forum.flag'));

        $this->addSuccessMessage('You are now watching the forum again');
        $this->addCallback('refresh');

        return $this->getJsonResponse(array());
    }

    /**
     * @Route("/api/forum/markAsRead", name="symbb_api_forum_mark_as_read")
     * @Method({"POST"})
     */
    public function forumMarkAsRead()
    {
        $id = (int) $this->get('request')->get('id');

        $forum = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')
            ->find($id);
        $this->get('symbb.core.forum.manager')->markAsRead($forum, $this->get('symbb.core.forum.flag'));

        $this->addSuccessMessage('The forum has been marked as read');
        $this->addCallback('refresh');

        return $this->getJsonResponse(array());
    }

    /**
     * @Route("/api/forum/data", name="symbb_api_forum_data")
     * @Method({"GET"})
     */
    public function forumDataAction()
    {

        $id = (int) $this->get('request')->get('id');
        if ((int) $id === 0) {
            $id = null;
        }

        $parent = null;
        if ($id > 0) {
            $parent = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')->find($id);
        } else {
            $parent = new Forum();
            $childs = $this->get('symbb.core.forum.manager')->findAll();
            $parent->setChildren($childs);
        }

        $breadcrumbItems = $this->get('symbb.core.forum.manager')->getBreadcrumbData($parent);
        $this->addBreadcrumbItems($breadcrumbItems);

        $params = array(
            'forum' => $this->getForumAsArray($parent)
        );

        return $this->getJsonResponse($params);
    }

    /**
     * 
     * @param \SymBB\Core\ForumBundle\Entity\Topic $topic
     * @return array
     */
    protected function getTopicAsArray(\SymBB\Core\ForumBundle\Entity\Topic $topic = null, $page = 1, $postSorting = 'asc', $addPaginationData = false)
    {

        $tags = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Topic\Tag', 'symbb')->findAll();

        $array = array();
        $array['id'] = 0;
        $array['name'] = '';
        $array['changed'] = 0;
        $array['created'] = 0;
        $array['locked'] = false;
        $array['notifyMe'] = false;
        $array['count']['post'] = 0;
        $array['backgroundImage'] = '';
        $array['flags'] = array();
        $array['posts'] = array();
        $array['seo']['name'] = '';
        $array['forum']['id'] = 0;
        $array['forum']['seo']['name'] = '';
        $array['access'] = array(
            'createPost' => false,
            'edit' => false,
            'delete' => false
        );
        $array['mainPost'] = $this->getPostAsArray();
        $array['author'] = $this->getAuthorAsArray();
        $array['tags'] = array();
        $array['paginationData'] = array();
        foreach($tags as $tag){

            $translation = $this->get('translator')->trans($tag->getName(), array(), 'symbb_variables');

            $array['tags'][$tag->getId()] = array(
                'id' => $tag->getId(),
                'priority' => $tag->getPriority(),
                'name' => $translation,
                'status' => 0
            );
        }
        if (is_object($topic) && \is_object($topic->getForum())) {


            $array['forum']['id'] = $topic->getForum()->getId();
            $array['forum']['seo']['name'] = $topic->getForum()->getSeoName();
            $array['forum']['name'] = $topic->getForum()->getName();

            if ($topic->getId() > 0) {
                $array['id'] = $topic->getId();
                $array['name'] = $topic->getName();
                $array['locked'] = $topic->isLocked();
                $array['changed'] = $this->getISO8601ForUser($topic->getChanged());
                $array['created'] = $this->getISO8601ForUser($topic->getCreated());
                $array['backgroundImage'] = $this->get('symbb.core.user.manager')->getAvatar($topic->getAuthor());
                foreach ($this->get('symbb.core.topic.flag')->findAll($topic) as $flag) {
                    $array['flags'][$flag->getFlag()] = $this->getFlagAsArray($flag);
                }
                $posts = $this->get('symbb.core.topic.manager')->findPosts($topic, $page, null, $postSorting);
                $paginationData = $posts->getPaginationData();
                if($addPaginationData){
                    $this->addPaginationData($posts);
                }
                $array['count']['post'] = $paginationData['totalCount'];
                foreach ($posts as $post) {
                    $array['posts'][] = $this->getPostAsArray($post);
                }
                $array['seo']['name'] = $topic->getSeoName();

                $array['notifyMe'] = $this->get('symbb.core.topic.flag')->checkFlag($topic, 'notify');
                if ($array['notifyMe'] > 0) {
                    $array['notifyMe'] = true;
                }

                $array['mainPost'] = $this->getPostAsArray($topic->getMainPost());
                $array['text'] = $array['mainPost']['text'];
                $array['rawText'] = $array['mainPost']['rawText'];
                $array['breadcrumb'] = $array['mainPost']['breadcrumb'];
                $array['author'] = $this->getAuthorAsArray($topic->getAuthor());
                foreach($tags as $tag){
                    $status = false;
                    foreach($topic->getTags() as $currTag){
                        if($tag->getId() == $currTag->getId()){
                            $status = true;
                            break;
                        }
                    }
                    $array['tags'][$tag->getId()]['status'] = $status;
                }


                $writePostAccess = $this->get('security.context')->isGranted('CREATE_POST', $topic->getForum());
                $editAccess = $this->get('security.context')->isGranted('EDIT', $topic);
                $deleteAccess = $this->get('security.context')->isGranted('DELETE', $topic);

                $array['access'] = array(
                    'createPost' => $writePostAccess,
                    'edit' => $editAccess,
                    'delete' => $deleteAccess
                );

                if ($topic->isLocked()) {
                    $array['access']['createPost'] = false;
                }
            }
        }

        $event = new \SymBB\Core\EventBundle\Event\ApiDataEvent($topic);
        $this->handleEvent('symbb.api.topic.data', $event);
        $array['extension'] = $event->getExtensionData();

        $extensionAccess = $event->getAccessData();
        $array['access'] = array_merge($array['access'], $extensionAccess);

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
        $array['isForum'] = false;
        $array['isCategory'] = false;
        $array['isLink'] = false;
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
                $array['children'][] = $this->getForumAsArray($child, false);
            }
            $lastPosts = $this->get('symbb.core.forum.manager')->findPosts($forum, 10);
            foreach ($lastPosts as $post) {
                $array['lastPosts'][] = $this->getPostAsArray($post, true);
            }
            $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
            if ($forum->getImageName()) {
                $array['backgroundImage'] = $helper->asset($forum, 'image');
            }

            $array['seo']['name'] = $forum->getSeoName();

            $writeAccess = $this->get('security.context')->isGranted('CREATE_TOPIC', $forum);
            $writePostAccess = $this->get('security.context')->isGranted('CREATE_POST', $forum);

            $array['access'] = array(
                'createTopic' => $writeAccess,
                'createPost' => $writePostAccess
            );

            $array['ignored'] = $this->get('symbb.core.forum.manager')->isIgnored($forum, $this->get('symbb.core.forum.flag'));

            if ($forum->getType() === 'link') {
                $link = $forum->getLink();
                if(strpos($link, 'http') !== 0){
                    $link = 'http://'.$link;
                }
                $array['isLink'] = true;
                $array['link'] = $link;
                $array['linkCalls'] = $forum->getCountLinkCalls();
            } else if ($forum->getType() === 'forum') {
                $array['isForum'] = true;
            } else {
                $array['isCategory'] = true;
            }
        }

        $event = new \SymBB\Core\EventBundle\Event\ApiDataEvent($forum);
        $this->handleEvent('symbb.api.forum.data', $event);
        $array['extension'] = $event->getExtensionData();

        $extensionAccess = $event->getAccessData();
        $array['access'] = array_merge($array['access'], $extensionAccess);

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
        } else if ($flagName == 'ignore') {
            $array['type'] = 'info';
        } else if ($flagName == 'locked') {
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
    protected function getPostAsArray(\SymBB\Core\ForumBundle\Entity\Post $post = null, $bshort = false)
    {

        $array = array();
        $array['id'] = 0;
        $array['name'] = '';
        $array['changed'] = 0;
        $array['created'] = 0;
        $array['files'] = array();
        $array['seo']['name'] = '';
        $array['topic']['id'] = 0;
        $array['topic']['name'] = '';
        $array['topic']['seo']['name'] = '';

        if(!$bshort){
            $array['text'] = '';
            $array['rawText'] = '';
            $array['signature'] = '';
            $array['access']['edit'] = false;
            $array['access']['delete'] = false;
            $array['notifyMe'] = false;
            $array['flags'] = array();
        }

        if (is_object($post)) {
            $array['id'] = (int) $post->getId();
            $array['name'] = $post->getName();
            $array['changed'] = $this->getISO8601ForUser($post->getChanged());
            $array['created'] = $this->getISO8601ForUser($post->getCreated());
            $array['seo']['name'] = $post->getSeoName();

            $array['author'] = $this->getAuthorAsArray($post->getAuthor());
            $array['name'] = $post->getTopic()->getName();

            $array['topic']['id'] = $post->getTopic()->getId();
            $array['topic']['name'] = $post->getTopic()->getName();
            $array['topic']['seo']['name'] = $post->getTopic()->getSeoName();

            if(!$bshort){
                $array['rawText'] = $post->getText();
                $array['text'] = $this->get('symbb.core.post.manager')->parseText($post);
                $array['signature'] = $this->get('symbb.core.user.manager')->getSignature($post->getAuthor());
                foreach ($this->get('symbb.core.post.flag')->findAll($post) as $flag) {
                    $array['flags'][$flag->getFlag()] = $this->getFlagAsArray($flag);
                }
                if ($post->getId() > 0) {

                    $array['notifyMe'] = $this->get('symbb.core.topic.flag')->checkFlag($post->getTopic(), 'notify');
                    if ($array['notifyMe'] > 0) {
                        $array['notifyMe'] = true;
                    }
                    foreach ($post->getFiles() as $file) {
                        $array['files'][] = $file->getPath();
                    }
                    $editAccess = $this->get('security.context')->isGranted('EDIT', $post);
                    $deleteAccess = $this->get('security.context')->isGranted('DELETE', $post);

                    $array['access'] = array(
                        'edit' => $editAccess,
                        'delete' => $deleteAccess
                    );
                }
            }
        } else {
            $array['author'] = $this->getAuthorAsArray();
        }

        if(!$bshort && is_object($post)){
            $event = new \SymBB\Core\EventBundle\Event\ApiDataEvent($post);
            $this->handleEvent('symbb.api.post.data', $event);
            $array['extension'] = $event->getExtensionData();

            $extensionAccess = $event->getAccessData();
            $array['access'] = array_merge($array['access'], $extensionAccess);
            $breadcrumbItems = $this->get('symbb.core.post.manager')->getBreadcrumbData($post, $this->get('symbb.core.topic.manager'), $this->get('symbb.core.forum.manager'));
            $array['breadcrumb'] = $breadcrumbItems;
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
            $array['created'] = $this->getISO8601ForUser($author->getCreated());
        }

        return $array;
    }

    protected function handlePostImages(\SymBB\Core\ForumBundle\Entity\Post $post, $files, $em)
    {
        if (!empty($files)) {
            $currentFiles = $post->getFiles();
            // check for new stuff
            foreach ($files as $file) {
                $found = false;
                foreach ($currentFiles as $currentFile) {
                    if ($currentFile->getPath() == $file) {
                        $found = true;
                    }
                }
                if (!$found) {
                    $newName = $this->get('symbb.core.upload.manager')->moveToSet('post', $file);
                    $text = \str_replace($file, $newName, $post->getText());
                    $post->setText($text);
                    $newFile = new \SymBB\Core\ForumBundle\Entity\Post\File();
                    $newFile->setPath($newName);
                    $newFile->setPost($post);
                    $post->addFile($newFile);
                }
            }
            // check for old stuff
            foreach ($currentFiles as $currentFile) {
                $found = false;
                foreach ($files as $file) {
                    if ($currentFile->getPath() == $file) {
                        $found = true;
                    }
                }
                if (!$found) {
                    $em->remove($currentFile);
                    $post->removeFile($currentFile);
                }
            }
        }
    }
}