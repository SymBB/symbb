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

    public function listAction($parent = null){

        $repo = $this->get('doctrine')->getRepository('SymBBCoreSiteBundle:Site', 'symbb');
        $sites = $repo->findAll();

        return $this->render(
            $this->getTemplateBundleName() . ':Acp/Site:list.html.twig', array('sites' => $sites)
        );
    }
}