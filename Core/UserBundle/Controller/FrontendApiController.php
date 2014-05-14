<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\UserBundle\Controller;

class FrontendApiController extends \SymBB\Core\SystemBundle\Controller\AbstractApiController
{

    public function userlistAction()
    {
        $page = $this->get('request')->get('page');
        if (!$page || $page < 1) {
            $page = 1;
        }
        $em = $this->getDoctrine()->getManager('symbb');
        $allFields = $em->getRepository('SymBBCoreUserBundle:Field')->findBy(array('displayInMemberlist' => true));
        $usermanager = $this->get('symbb.core.user.manager');
        $users = $usermanager->findBy(array('symbbType' => 'user'), 20, $page);


        $params = array();
        $params['userfields'] = array();
        foreach ($allFields as $field) {
            $params['userfields'][] = array(
                'id' => $field->getId(),
                'label' => $field->getLabel(),
                'dataType' => $field->getDataType(),
                'formType' => $field->getFormType()
            );
        }

        $params['entries'] = array();

        foreach ($users as $user) {
            $userdata = array(
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'created' => $this->getISO8601ForUser($user->getCreated()),
                'lastLogin' => $this->getISO8601ForUser($user->getLastLogin()),
                'count' => array(
                    'post' => $usermanager->getPostCount(),
                    'topic' => $usermanager->getTopicCount()
                )
            );
            foreach ($allFields as $field) {
                $value = $user->getFieldValue($field)->getValue();
                $userdata['fields'][] = array(
                    'id' => $field->getId(),
                    'value' => $value
                );
            }
            $params['entries'][] = $userdata;
        }

        $this->addPaginationData($users);
        return $this->getJsonResponse($params);
    }

    public function ucpDataAction()
    {
        $user = $this->getUser();
        $data = $user->getSymbbData();
        $params = array();
        $params['user'] = array();
        $params['user']['data']['avatar'] = $data->getAvatar();
        $params['user']['data']['signature'] = $data->getSignature();
        $params['user']['fields'] = array();
        $em = $this->getDoctrine()->getManager('symbb');
        $allFields = $em->getRepository('SymBBCoreUserBundle:Field')->findAll();
        foreach ($allFields as $field) {
            $value = $user->getFieldValue($field)->getValue();
            $params['user']['fields'][] = array(
                'id' => $field->getId(),
                'value' => $value,
                'dataType' => $field->getDataType(),
                'formType' => $field->getFormType(),
                'label' => $field->getLabel()
            );
        }
        $params['user']['passwordChange']['password'] = '';
        $params['user']['passwordChange']['repeat'] = '';
        return $this->getJsonResponse($params);
    }

    public function ucpSaveAction()
    {
        $user = $this->getUser();
        $request = $this->get('request');
        $userData = $request->get('data');
        $fields = $request->get('fields');
        $avatar = $userData['avatar'];
        $signature = $userData['signature'];

        $symbbData = $user->getSymbbData();
        if ($avatar) {
            $symbbData->setAvatar($avatar);
        }
        if ($signature) {
            $symbbData->setSignature($signature);
        }

        $em = $this->getDoctrine()->getManager('symbb');
        $allFields = $em->getRepository('SymBBCoreUserBundle:Field')->findAll();
        foreach ($allFields as $field) {
            $currFieldValue = $user->getFieldValue($field);
            foreach ($fields as $fieldValue) {
                if ($fieldValue['id'] == $field->getId()) {
                    $value = $fieldValue['value'];
                    $currFieldValue->setValue($value);
                    $em->persist($currFieldValue);
                    break;
                }
            }
        }

        $passwordRepeat = $request->get('passwordRepeat');
        if (!empty($passwordRepeat['repeat']) and $passwordRepeat['password'] === $passwordRepeat['repeat']) {
            $this->get('symbb.core.user.manager')->changeUserPassword($user, $passwordRepeat['password']);
        }


        $this->get('symbb.core.user.manager')->updateUserData($symbbData);

        $this->addSuccessMessage("saved successfully.");

        return $this->getJsonResponse(array());
    }
}