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

class OptionController extends \SymBB\Core\SystemBundle\Controller\AbstractController
{

    public function indexAction()
    {

        $user = $this->getUser();
        $data = $this->get('symbb.core.user.manager')->getSymbbData($user);

        $avatar = $data->getAvatar();

        if (empty($avatar)) {
            $gravatar = new \SymBB\Core\UserBundle\GravatarApi();
            $check = $gravatar->exists($user->getEmail());
            if ($check) {
                $avatar = $gravatar->getUrl($user->getEmail());
                $data->setAvatar($avatar);
            }
        }

        $form = $this->createForm(new Option($this->getDoctrine()->getManager('symbb'), $user), $data);

        $form->handleRequest($this->get('request'));

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager('symbb');

            $fields = $em->getRepository('SymBBCoreUserBundle:Field')->findAll();
            foreach ($fields as $field) {
                $fieldData = $form->get('field:' . $field->getId());
                $value = $fieldData->getData();
                $currFieldValue = $user->getFieldValue($field);
                $currFieldValue->setValue($value);
                $em->persist($currFieldValue);
            }

            $em->persist($data);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success', $this->get('translator')->trans('Your changes were saved!', array(), 'symbb_frontend')
            );
        }

        return $this->render(
                $this->getTemplateBundleName('forum') . ':User:options.html.twig', array('form' => $form->createView(), 'avatar' => $avatar)
        );
    }

    public function securityAction()
    {

        $user = $this->getUser();
        $form = $this->createForm(new SecurityOption($this->get('symbb.core.user.manager')), $user);
        $form->handleRequest($this->get('request'));

        if ($form->isValid()) {
            $password = $form->get('password')->getData();
            $this->get('symbb.core.user.manager')->changeUserPassword($user, $password);
            $this->get('session')->getFlashBag()->add(
                'success', $this->get('translator')->trans('Your changes were saved!', array(), 'symbb_frontend')
            );
        }

        return $this->render(
                $this->getTemplateBundleName('forum') . ':User:options_security.html.twig', array('form' => $form->createView())
        );
    }
}