<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\TapatalkBundle\Manager;

use Monolog\Logger;
use Symbb\Core\MessageBundle\DependencyInjection\MessageManager;
use \Symbb\Core\SystemBundle\Manager\AccessManager;
use \Symbb\Core\UserBundle\Manager\UserManager;
use \Symbb\Core\ForumBundle\DependencyInjection\ForumManager;
use \Symbb\Core\ForumBundle\DependencyInjection\TopicManager;
use \Symbb\Core\ForumBundle\DependencyInjection\PostManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @var MessageManager
     */
    protected $messageManager;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Request
     */
    protected $request;

    public function __construct(AccessManager $accessManager, UserManager $userManager, ForumManager $forumManager, TopicManager $topicManager, PostManager $postManager, Logger $logger, MessageManager $messageManager)
    {
        $this->accessManager = $accessManager;
        $this->userManager = $userManager;
        $this->forumManager = $forumManager;
        $this->topicManager = $topicManager;
        $this->postManager = $postManager;
        $this->logger = $logger;
        $this->messageManager = $messageManager;
    }

    public function setContainer(Container $container){
        $this->request = $container->get('request');
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
        foreach($this->request->cookies->all() as $name => $cookie){
            $response->headers->setCookie(new Cookie($name, $cookie));
        }
        return $response;
    }

    /**
     * @param $startNumber
     * @param $lastNumber
     * @param $limit
     * @param $page
     */
    public function calcLimitandPage($startNumber, $lastNumber, &$limit, &$page)
    {

        $limit = $lastNumber - $startNumber;
        if($startNumber <= 0){
            $startNumber = 1;
        }
        $page = \ceil($startNumber / $limit);
    }

    /**
     * @param $text
     * @return string
     */
    protected function createShortContent($text){
        return substr($text, 0 , 200);
    }

    /**
     * @param §message $
     * @param $data
     */
    public function debug($message, $data = array()){
        $this->logger->debug('Tapatalk: '.$message, $data);
    }
}