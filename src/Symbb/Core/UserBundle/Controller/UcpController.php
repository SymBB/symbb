<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\UserBundle\Controller;

use Symbb\Core\ForumBundle\DependencyInjection\TopicFlagHandler;
use Symbb\Core\ForumBundle\DependencyInjection\TopicManager;
use Symbb\Core\ForumBundle\Entity\Topic;
use Symbb\Core\SystemBundle\Controller\AbstractController;
use Symbb\Core\SystemBundle\Manager\AbstractFlagHandler;
use Symbb\Core\UserBundle\Entity\UserInterface;
use Symbb\Core\UserBundle\Form\Type\Option;
use Symbb\Core\UserBundle\Form\Type\SecurityOption;
use Symbb\Core\UserBundle\Manager\UserManager;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class UcpController extends AbstractController
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {

        /**
         * @var $usermanager UserManager
         */
        $usermanager = $this->get('symbb.core.user.manager');

        /**
         * get the current user
         * @var $user UserInterface
         */
        $user = $usermanager->getCurrentUser();
        $userData = $user->getSymbbData();

        //only user can access this page
        if (!is_object($user) || $user->getSymbbType() != "user") {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager('symbb');
        $fields = $em->getRepository('SymbbCoreUserBundle:Field')->findBy(array(), array('position' => 'asc', 'id' => 'asc'));
        $formType = new Option($fields, $user);
        $form = $this->createForm($formType, $userData);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $usermanager->updateUserData($userData, false);
            foreach ($fields as $field) {
                $currFieldValue = $user->getFieldValue($field);
                $data = $form->get("field_" . $field->getId())->getData();
                $currFieldValue->setValue($data);
                $em->persist($currFieldValue);
            }
            $this->addSavedSuccess($request);
            $em->flush();
        }


        return $this->render($this->getTemplateBundleName('forum') . ':Ucp:index.html.twig', array("form" => $form->createView()));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function securityAction(Request $request)
    {

        /**
         * @var $usermanager UserManager
         */
        $usermanager = $this->get('symbb.core.user.manager');

        /**
         * get the current user
         * @var $user UserInterface
         */
        $user = $usermanager->getCurrentUser();

        //only user can access this page
        if (!is_object($user) || $user->getSymbbType() != "user") {
            throw $this->createAccessDeniedException();
        }

        $formType = new SecurityOption($usermanager);
        $form = $this->createForm($formType, $user);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $errors = $usermanager->changeUserPassword($user, $user->getPlainPassword());
            if($errors->count() == 0){
                $this->addSavedSuccess($request);
            } else {
                $errorsString = (string) $errors;
                $error = new FormError($errorsString);
                $form->get("plainPassword")->addError($error);
            }
        }

        return $this->render($this->getTemplateBundleName('forum') . ':Ucp:security.html.twig', array("form" => $form->createView()));
    }

    public function notificationAction(Request $request){

        $topicIds = array();
        $page = $request->get("page", 1);
        /**
         * @var $topicManager TopicManager
         */
        $topicManager = $this->get("symbb.core.topic.manager");
        /**
         * @var $topicFlagHandler TopicFlagHandler
         */
        $topicFlagHandler = $this->get("symbb.core.topic.flag");
        $topic = new Topic();
        $flags = $topicFlagHandler->findFlagsByClassAndFlag($topic, AbstractFlagHandler::FLAG_NOTIFY, $this->getUser());
        foreach($flags as $flag){
            $topicIds[] = $flag->getObjectId();
        }
        $topics = $topicManager->findBy(array("id" => array("operator" => "IN", "value" => $topicIds)), $page);

        return $this->render($this->getTemplateBundleName('forum') . ':Ucp:notification.html.twig', array("topics" => $topics));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function notificationUnsubscribeAction(Request $request){
        $topicId = $request->get("id");
        /**
         * @var $topicManager TopicManager
         */
        $topicManager = $this->get("symbb.core.topic.manager");
        $topic = $topicManager->find($topicId);
        $topicManager->unsubscribeNotification($topic);

        return $this->notificationAction($request);
    }
}