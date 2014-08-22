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
use SymBB\Core\MessageBundle\Entity\Message;
use Symfony\Component\HttpFoundation\Request;

class FrontendApiController extends \SymBB\Core\SystemBundle\Controller\AbstractApiController
{

    /**
     * @Route("/api/message/list", name="symbb_api_message_list")
     * @Method({"GET"})
     */
    public function listAction(Request $request)
    {
        $params['entries'] = array();
        $messages = $this->get('symbb.core.message.manager')->findReceivedMessages();
        $breadcrumb = $this->getBreadcrumbData();
        $this->addBreadcrumbItems($breadcrumb);
        $this->addPaginationData($messages);
        foreach ($messages as $message) {
            $params['entries'][] = $this->getMessageAsArray($message);
        }
        $params['count']['received'] = $this->paginationData['totalCount'];
        return $this->getJsonResponse($params);
    }

    protected function getMessageAsArray(Message $message){
        $data = array(
            'id' => $message->getId(),
            'subject' => $message->getSubject(),
            'message' => $message->getMessage(),
            'date' => $this->getISO8601ForUser($message->getDate()),
            'sender' => $this->getUserAsArray($message->getSender())
        );

        return $data;
    }

    protected function getUserAsArray(\SymBB\Core\UserBundle\Entity\UserInterface $user)
    {
        $array = array();
        $array['id'] = 0;
        $array['username'] = '';
        $array['avatar'] = '';

        if (is_object($user)) {
            $array['id'] = $user->getId();
            $array['username'] = $user->getUsername();
            $array['avatar'] = $this->get('symbb.core.user.manager')->getAbsoluteAvatarUrl($user);
        }

        return $array;
    }

    protected function getBreadcrumbData($message = null)
    {
        $breadcrumb = array();

        if($message){
            $breadcrumb[] = array(
                'type' => 'message',
                'name' => $message->getSubject(),
                'seoName' => $message->getSubject(),
                'id' => $message->getId()
            );
        }

        $home = $this->get('translator')->trans('Home', array(), 'symbb_frontend');
        $breadcrumb[] = array('name' => $home, 'type' => 'home');
        $breadcrumb = array_reverse($breadcrumb);

        return $breadcrumb;
    }
}