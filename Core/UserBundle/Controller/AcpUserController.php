<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\UserBundle\Controller;

class AcpUserController extends \SymBB\Core\AdminBundle\Controller\Base\CrudController
{

    protected $entityBundle = 'SymBBCoreUserBundle';

    protected $entityName = 'User';

    protected $formClass = '\SymBB\Core\UserBundle\Form\Type\User';

    protected function getForm()
    {
        $entity = $this->getFormEntity();
        $form = $this->createForm(new $this->formClass($this->get('event_dispatcher'), $this->get('symbb.core.user.manager')), $entity);
        return $form;
    }

    public function enableAction($id)
    {
        $user = $this->getRepository()->find($id);
        if (\is_object($user)) {
            $user->enable();
            $this->get('symbb.core.user.manager')->updateUser($user);
        }
        return $this->listAction();
    }

    public function disableAction($id)
    {
        $user = $this->getRepository()->find($id);
        if (\is_object($user)) {
            $user->disable();
            $this->get('symbb.core.user.manager')->updateUser($user);
        }
        return $this->listAction();
    }
}