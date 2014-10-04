<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\TapatalkBundle\Manager;

use \Symbb\Core\SystemBundle\DependencyInjection\AccessManager;
use \Symbb\Core\UserBundle\DependencyInjection\UserManager;
use \Symbb\Core\ForumBundle\DependencyInjection\ForumManager;
use \Symbb\Core\ForumBundle\DependencyInjection\TopicManager;
use \Symbb\Core\ForumBundle\DependencyInjection\PostManager;

class AbstractManager
{

    /**
     *
     * @var \Symbb\Core\SystemBundle\DependencyInjection\AccessManager
     */
    protected $accessManager;

    /**
     *
     * @var \Symbb\Core\UserBundle\DependencyInjection\UserManager
     */
    protected $userManager;

    /**
     *
     * @var \Symbb\Core\ForumBundle\DependencyInjection\ForumManager
     */
    protected $forumManager;

    /**
     *
     * @var \Symbb\Core\ForumBundle\DependencyInjection\TopicManager
     */
    protected $topicManager;

    /**
     *
     * @var \Symbb\Core\ForumBundle\DependencyInjection\PostManager
     */
    protected $postManager;

    public function __construct(AccessManager $accessManager, UserManager $userManager, ForumManager $forumManager, TopicManager $topicManager, PostManager $postManager)
    {
        $this->accessManager = $accessManager;
        $this->userManager = $userManager;
        $this->forumManager = $forumManager;
        $this->topicManager = $topicManager;
        $this->postManager = $postManager;
    }

    protected function getResponse($value, $type)
    {
        $value = \Zend\XmlRpc\AbstractValue::getXmlRpcValue($value, $type);
        $generator = \Zend\XmlRpc\AbstractValue::getGenerator();
        $generator->openElement('methodResponse')
            ->openElement('params')
            ->openElement('param');
        $value->generateXml();
        $generator->closeElement('param')
            ->closeElement('params')
            ->closeElement('methodResponse');

        $content = $generator->flush();
        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');
        $response->setContent($content);
        return $response;
    }

    public function calcLimitandPage($startNumber, $lastNumber, &$limit, &$page)
    {
        if ($startNumber && $lastNumber) {
            $limit = $lastNumber - $startNumber;
            $page = \ceil($startNumber / $limit);
        }
    }
}