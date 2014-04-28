<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\BBCodeBundle\Controller;

class AcpController extends \SymBB\Core\AdminBundle\Controller\Base\CrudController
{

    protected $entityBundle = 'SymBBCoreBBCodeBundle';

    protected $entityName = 'BBCode';

    protected $formClass = '\SymBB\Core\BBCodeBundle\Form\Type\BBCode';

    protected function getForm()
    {
        $entity = $this->getFormEntity();
        $form = $this->createForm(new $this->formClass($this->get('translator'), $this->get('doctrine.orm.symbb_entity_manager')), $entity);
        return $form;
    }
}