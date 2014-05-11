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

    public function ucpDataAction()
    {
        $user = $this->getUser();
        $data = $user->getSymbbData();
        $params = array();
        $params['user'] = array();
        $params['user']['data']['avatar'] = $data->getAvatar();
        $params['user']['data']['signature'] = $data->getSignature();
        $params['user']['passwordChange']['password'] = '';
        $params['user']['passwordChange']['repeat'] = '';
        return $this->getJsonResponse($params);
    }

    public function ucpSaveAction()
    {
        $user       = $this->getUser();
        $request    = $this->get('request');
        $userData   = $request->get('data');
        $avatar     = $userData['avatar'];
        $signature  = $userData['signature'];

        $symbbData = $user->getSymbbData();
        if($avatar){
            $symbbData->setAvatar($avatar);
        }
        if($signature){
            $symbbData->setSignature($signature);
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