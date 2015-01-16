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
use Symbb\Core\UserBundle\Entity\UserInterface;
use Symbb\Core\UserBundle\Form\Type\Option;
use Symfony\Component\HttpFoundation\Request;

class UcpController extends AbstractController
{

    public function indexAction(Request $request)
    {
        $usermanager = $this->get('symbb.core.user.manager');
        /**
         * @var $user UserInterface
         */
        $user = $usermanager->getCurrentUser();

        if (!is_object($user) || $user->getSymbbType() != "user") {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager('symbb');
        $formType = new Option($em, $user);
        $form = $this->createForm($formType, $user->getSymbbData());

        return $this->render($this->getTemplateBundleName('forum') . ':Ucp:index.html.twig', array("form" => $form->createView()));
    }


}