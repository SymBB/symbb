<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SiteBundle\Controller;

class AcpSiteController extends \SymBB\Core\AdminBundle\Controller\Base\CrudController
{

    protected $entityBundle = 'SymBBCoreSiteBundle';

    protected $entityName = 'Site';

    protected $formClass = '\SymBB\Core\SiteBundle\Form\Type\Site';

    

    protected function getForm()
    {
        $entity = $this->getFormEntity();
        $form = $this->createForm(new $this->formClass($this->get('event_dispatcher')), $entity);
        return $form;
    }
    
    
    protected function addListParams($params, $parent = null)
    {
        if ($parent) {
            $params['parent'] = $parent;
        } else {
            $params['parent'] = 0;
        }
        
        $allEntries = $this->findListEntities(null);
        $params['allEntries'] = $allEntries;
        
        return $params;
    }
}