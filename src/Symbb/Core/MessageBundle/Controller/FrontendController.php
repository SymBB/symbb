<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\MessageBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symbb\Core\MessageBundle\DependencyInjection\MessageManager;
use Symbb\Core\MessageBundle\Entity\Message;
use Symbb\Core\SystemBundle\Controller\AbstractController;
use Symbb\Core\UserBundle\Manager\UserManager;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FrontendController
 * @package Symbb\Core\MessageBundle\Controller
 */
class FrontendController extends AbstractController
{


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function receivedListAction(Request $request){
        $page = $request->get("page", 1);
        /**
         * @var $manager MessageManager
         */
        $manager = $this->get('symbb.core.message.manager');
        $messages = $manager->findReceivedMessages(null, $page);

        return $this->render($this->getTemplateBundleName('forum') . ':Message:receivedList.html.twig', array("messages" => $messages));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sentListAction(Request $request){
        $page = $request->get("page", 1);
        /**
         * @var $manager MessageManager
         */
        $manager = $this->get('symbb.core.message.manager');
        $messages = $manager->findSentMessages(null, $page);

        return $this->render($this->getTemplateBundleName('forum') . ':Message:sentList.html.twig', array("messages" => $messages));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request){
        $id = $request->get("id");
        if ($id > 0) {
            $message = $this->get('symbb.core.message.manager')->find($id);
            $isReceiver = false;
            $isSender = false;
            foreach ($message->getReceivers() as $receiver) {
                if ($receiver->getUser()->getId() == $this->getUser()->getId()) {
                    $isReceiver = true;
                    $this->get('symbb.core.message.manager')->read($receiver);
                    break;
                }
            }

            $sender = $message->getSender();
            if ($this->getUser()->getId() === $sender->getid()) {
                $isSender = true;
            }

            if(!$isReceiver && !$isSender){
                throw $this->createAccessDeniedException();
            }

            $newMessage = new Message();
            $newMessage->setSender($this->getUser());
            $receiver = new Message\Receiver();
            $receiver->setUser($sender);
            $receiver->setMessage($newMessage);
            $newMessage->addReceiver($receiver);
            $newMessage->setSubject($this->get("translator")->trans("Re:", array(), "symbb_frontend")." ".$message->getSubject());

            $form = $this->getForm($newMessage, $request, false);

            $form->handleRequest($request);
            $saved = false;

            if ($form->isValid()) {
                $saved = $this->handleMessage($form, $newMessage);
            }

            if(!$saved){
                $breadcrumb = $this->getBreadcrumbData($message);
                return $this->render($this->getTemplateBundleName('forum') . ':Message:show.html.twig', array("message" => $message, "breadcrumb" => $breadcrumb, "isReceiver" => $isReceiver, "isSender" => $isSender, "form" => $form->createView()));
            } else {
                return $this->sentListAction($request);
            }

        } else {
            $this->addError("Message not found!", $request);
        }
        return $this->returnToLastPage($request);
    }

    /**
     * @param Form $form
     * @param Message $message
     * @return bool
     */
    public function handleMessage(Form $form, Message $message){
        $errors = $this->get('symbb.core.message.manager')->sendMessage($message);
        if($errors->count() == 0){
            return true;
        } else {
            $form->addError(new FormError($errors));
        }
        return false;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request){
        $newMessage = new Message();
        $newMessage->setSender($this->getUser());
        $form = $this->getForm($newMessage, $request);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $saved = $this->handleMessage($form, $newMessage);
            if($saved){
                return $this->sentListAction($request);
            }
        }
        return $this->render($this->getTemplateBundleName('forum') . ':Message:new.html.twig', array("form" => $form->createView()));
    }

    /**
     * @param Message $message
     * @param Request $request
     * @param bool $full
     * @return Form
     */
    public function getForm(Message $message, Request $request, $full = true){
        $form = $this->createForm(new \Symbb\Core\MessageBundle\Form\Message($full, $this->get("symbb.core.user.manager"), $message), $message, array(
            'cascade_validation' => true,
            'error_bubbling' => true
        ));
        return $form;
    }


    /**
     * @param null $message
     * @return array
     */
    protected function getBreadcrumbData($message = null)
    {
        $breadcrumb = array();

        if ($message) {
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