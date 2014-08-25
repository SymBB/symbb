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
use SymBB\Core\UserBundle\DependencyInjection\UserManager;
use SymBB\Core\UserBundle\Entity\UserInterface;
use Symfony\Component\HttpFoundation\Request;

class FrontendApiController extends \SymBB\Core\SystemBundle\Controller\AbstractApiController
{

    /**
     * @Route("/api/message/save", name="symbb_api_message_save")
     * @Method({"POST"})
     */
    public function saveAction(Request $request)
    {

        $subject = $request->get('subject');
        $message = $request->get('message');
        $userManager = $this->get('symbb.core.user.manager');
        /**
         * @var $userManager UserManager
         */
        $receiverIds = (array)$request->get('receivers');
        $receivers = array();
        foreach($receiverIds as $receiverId){
            $receivers[] = $userManager->find((int)$receiverId);
        }

        $errors = $this->get('symbb.core.message.manager')->sendMessage($subject, $message, $receivers);

        if(!empty($errors)){
            foreach($errors as $error){
                $this->addErrorMessage($error);
            }
        }

        return $this->getJsonResponse();
    }

    /**
     * @Route("/api/message/list/received", name="symbb_api_message_list_received")
     * @Method({"GET"})
     */
    public function receviedListAction(Request $request)
    {
        $params['entries'] = array();
        $messages = $this->get('symbb.core.message.manager')->findReceivedMessages();
        $breadcrumb = $this->getBreadcrumbData();
        $this->addBreadcrumbItems($breadcrumb);
        $this->addPaginationData($messages);
        foreach ($messages as $message) {
            $params['entries'][] = $this->getMessageAsArray($message);
        }
        return $this->getJsonResponse($params);
    }

    /**
     * @Route("/api/message/list/sent", name="symbb_api_message_list_sent")
     * @Method({"GET"})
     */
    public function sentListAction(Request $request)
    {
        $params['entries'] = array();
        $messages = $this->get('symbb.core.message.manager')->findSentMessages();
        $breadcrumb = $this->getBreadcrumbData();
        $this->addBreadcrumbItems($breadcrumb);
        $this->addPaginationData($messages);
        foreach ($messages as $message) {
            $params['entries'][] = $this->getMessageAsArray($message);
        }
        return $this->getJsonResponse($params);
    }

    /**
     * @Route("/api/message/data", name="symbb_api_message_data")
     * @Method({"GET"})
     */
    public function dataAction(Request $request)
    {
        $id = (int)$request->get('id');
        $params = array();
        if($id > 0){
            $message = $this->get('symbb.core.message.manager')->find($id);
            $breadcrumb = $this->getBreadcrumbData($message);
            $this->addBreadcrumbItems($breadcrumb);
            $params['message'] = $this->getMessageAsArray($message);
        }
        return $this->getJsonResponse($params);
    }

    protected function getMessageAsArray(Message $message){

        $receivers = array();
        foreach($message->getReceivers() as $receiver){
            $receivers[] = $this->getUserAsArray($receiver->getUser());
        }

        $data = array(
            'id' => $message->getId(),
            'subject' => $message->getSubject(),
            'messageRaw' => $message->getMessage(),
            'message' => $this->get('symbb.core.message.manager')->parseMessage($message),
            'date' => $this->getISO8601ForUser($message->getDate()),
            'sender' => $this->getUserAsArray($message->getSender()),
            'receivers' => $receivers
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

        $home = $this->get('translator')->trans('Messages', array(), 'symbb_frontend');
        $breadcrumb[] = array('name' => $home, 'type' => 'message_home');

        $home = $this->get('translator')->trans('Home', array(), 'symbb_frontend');
        $breadcrumb[] = array('name' => $home, 'type' => 'home');
        $breadcrumb = array_reverse($breadcrumb);

        return $breadcrumb;
    }
}