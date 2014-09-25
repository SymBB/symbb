<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\UserBundle\Controller;

class AcpFieldController extends \Symbb\Core\AdminBundle\Controller\Base\CrudController
{

    protected $entityBundle = 'SymbbCoreUserBundle';

    protected $entityName = 'Field';

    protected $formClass = '\Symbb\Core\UserBundle\Form\Type\Field';

    protected function getTemplateDirectory()
    {
        return 'UserField';
    }

    protected function findListEntities($parent = null)
    {
        $entityList = $this->getRepository()->findBy(array(), array('position' => 'ASC'));
        return $entityList;
    }
    
}