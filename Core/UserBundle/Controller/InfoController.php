<?php

/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\UserBundle\Controller;

use \SymBB\Core\UserBundle\Form\Type\Option;
use \SymBB\Core\UserBundle\Form\Type\SecurityOption;
use Symfony\Component\HttpFoundation\Request;

class InfoController extends \SymBB\Core\SystemBundle\Controller\AbstractController
{

    public function userlistAction(Request $request)
    {

        $users = $this->get('symbb.core.user.manager')->paginateAll($request);

        return $this->render(
            $this->getTemplateBundleName('forum') . ':User:userlist.html.twig', array('users' => $users)
        );
    }

    public function profileAction($id)
    {

        $user = $this->get('symbb.core.user.manager')->find($id);

        return $this->render(
            $this->getTemplateBundleName('forum') . ':User:profile.html.twig', array('user' => $user)
        );
    }
}