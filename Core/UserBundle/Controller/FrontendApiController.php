<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class FrontendApiController extends \SymBB\Core\SystemBundle\Controller\AbstractApiController
{
    public function memberlistAction()
    {
        $page = $this->get('request')->get('page');
        if (!$page || $page < 1) {
            $page = 1;
        }
        $em = $this->getDoctrine()->getManager('symbb');
        $allFields = $em->getRepository('SymBBCoreUserBundle:Field')->findAll();
        $usermanager = $this->get('symbb.core.user.manager');
        $users = $usermanager->findBy(array(), $page, 20);
        
        
        $params = array();
        $params['user']['fields'] = array();
        foreach ($allFields as $field) {
            $params['user']['fields'][] =  array(
                'id' => $field->getId(),
                'label' => $field->getLabel(),
                'dataType' => $field->getDataType(),
                'formType' => $field->getFormType()
            );
        }
        
        
        foreach ($users as $user) {
            $userdata = array(
                'username' => $user->getUsername(),
                'created' => $user->getCreated()->getTimestamp(),
                'lastLogin' => $user->getLastLogin()->getTimestamp(),
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
            $params['list'][] =  $userdata;
        }
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
        $user       = $this->getUser();
        $request    = $this->get('request');
        $userData   = $request->get('data');
        $fields     = $request->get('fields');
        $avatar     = $userData['avatar'];
        $signature  = $userData['signature'];

        $symbbData = $user->getSymbbData();
        if($avatar){
            $symbbData->setAvatar($avatar);
        }
        if($signature){
            $symbbData->setSignature($signature);
        }

        $em = $this->getDoctrine()->getManager('symbb');
        $allFields = $em->getRepository('SymBBCoreUserBundle:Field')->findAll();
        foreach ($allFields as $field) {
            $currFieldValue = $user->getFieldValue($field);
            foreach($fields as $fieldValue){
                if($fieldValue['id'] == $field->getId()){
                    $value = $fieldValue['value'];
                    $currFieldValue->setValue($value);
                    $em->persist($currFieldValue);
                    break;
                }
            }
        }

        $passwordRepeat = $request->get('passwordRepeat');
        if(!empty($passwordRepeat['repeat']) and $passwordRepeat['password'] === $passwordRepeat['repeat']){
            $this->get('symbb.core.user.manager')->changeUserPassword($user, $passwordRepeat['password']);
        }


        $this->get('symbb.core.user.manager')->updateUserData($symbbData);

        $this->addSuccessMessage("saved successfully.");

        return $this->getJsonResponse(array());
    }

}