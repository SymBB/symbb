<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\UserBundle\Controller;
use SymBB\Core\UserBundle\Entity\UserInterface;
use Symfony\Component\HttpFoundation\Request;

class FrontendApiController extends \SymBB\Core\SystemBundle\Controller\AbstractApiController
{

    public function searchAction(Request $request)
    {
        $limit = 20;
        if($request->get('limit') !== null){
            $limit = (int)$request->get('limit');
        }

        $search = array('symbbType' => 'user');
        if($request->get('q') !== null){
            $query = (string)$request->get('q');
            $search['username'] = array('LIKE', '%'.$query.'%');
        }

        $usermanager = $this->get('symbb.core.user.manager');
        $users = $usermanager->findBy($search, $limit, 1);
        $this->addPaginationData($users);
        $data = array('entries' => array());
        foreach($users as $user){
            $data['entries'][] = $this->getUserAsArray($user);
        }

        return $this->getJsonResponse($data);
    }

    protected function getUserAsArray(UserInterface $user){
        $data = $this->get('symbb.core.user.manager')->getSymbbData($user);
        return array(
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'avatar' =>$this->get('symbb.core.user.manager')->getAvatar($user)
        );
    }
    public function userlistAction(Request $request)
    {
        $page = $request->get('page');
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
                    'post' => $usermanager->getPostCount($user),
                    'topic' => $usermanager->getTopicCount($user)
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
        $data = $this->get('symbb.core.user.manager')->getSymbbData($user);
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

    public function ucpSaveAction(Request $request)
    {
        $user = $this->getUser();
        $userData = $request->get('data');
        $fields = $request->get('fields');
        $avatar = $userData['avatar'];
        $signature = $userData['signature'];

        $symbbData = $this->get('symbb.core.user.manager')->getSymbbData($user);
        if ($avatar) {
            $symbbData->setAvatar($avatar);
        }
        if ($signature) {
            $symbbData->setSignature($signature);
        }

        $passwordRepeat = $request->get('changePassword');
        $errors = new \Symfony\Component\Validator\ConstraintViolationList();
        if (!empty($passwordRepeat['repeat']) and $passwordRepeat['password'] === $passwordRepeat['repeat']) {
            $errors = $this->get('symbb.core.user.manager')->changeUserPassword($user, $passwordRepeat['password']);
            foreach($errors as $key => $error){
                $this->addErrorMessage($this->trans('Passwort').': '.$error->getMessage());
            }
        }

        if($errors->count() === 0){
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

            $errors = $this->get('symbb.core.user.manager')->updateUserData($symbbData);

            if($errors->count() === 0){
                $this->addSuccessMessage("saved successfully.");
            } else {
                foreach($errors as $key => $error){
                    $this->addErrorMessage($this->trans($key).': '.$error->getMessage());
                }
            }
        }

        return $this->getJsonResponse(array());
    }
}