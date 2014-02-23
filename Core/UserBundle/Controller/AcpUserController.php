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
        $form = $this->createForm(new $this->formClass($this->get('event_dispatcher')), $entity);
        return $form;
    }
}