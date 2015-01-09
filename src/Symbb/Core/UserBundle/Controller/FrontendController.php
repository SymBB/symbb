<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\UserBundle\Controller;
use Symbb\Core\SystemBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class FrontendController extends AbstractController
{

    public function viewAction(Request $request)
    {
        $usermanager = $this->get('symbb.core.user.manager');
        $user = $usermanager->find($request->get("id"));

        $em = $this->getDoctrine()->getManager('symbb');
        $allFields = $em->getRepository('SymbbCoreUserBundle:Field')->findAll();

        $params = array();
        $params['userfields'] = $allFields;
        $params['profileUser'] = $user;

        $response = $this->render(
            $this->getTemplateBundleName('forum').':User:view.html.twig',
            $params
        );

        return $response;
    }

    public function userlistAction(Request $request)
    {
        $page = $request->get('page');
        if (!$page || $page < 1) {
            $page = 1;
        }
        $usermanager = $this->get('symbb.core.user.manager');
        $users = $usermanager->findBy(array('symbbType' => 'user'), 20, $page);


        $userFieldFilter = array('displayInMemberlist' => true);
        $em = $this->getDoctrine()->getManager('symbb');
        $allFields = $em->getRepository('SymbbCoreUserBundle:Field')->findBy($userFieldFilter);
        $params = array();
        $params['userfields'] = $allFields;
        $params['entries'] = $users;

        $response = $this->render(
            $this->getTemplateBundleName('forum').':User:list.html.twig',
            $params
        );

        return $response;
    }

}