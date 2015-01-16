<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class AcpSiteController extends \Symbb\Core\AdminBundle\Controller\Base\CrudController
{

    protected $entityBundle = 'SymbbCoreSiteBundle';

    protected $entityName = 'Site';

    protected $formClass = '\Symbb\Core\SiteBundle\Form\Type\Site';


    protected function getForm(Request $request)
    {
        $entity = $this->getFormEntity($request);
        $form = $this->createForm(new $this->formClass($this->get('event_dispatcher')), $entity);
        return $form;
    }

    public function listAction($parent = null)
    {

        $repo = $this->get('doctrine')->getRepository('SymbbCoreSiteBundle:Site', 'symbb');
        $sites = $repo->findAll();

        return $this->render(
            $this->getTemplateBundleName() . ':Acp/Site:list.html.twig', array('sites' => $sites)
        );
    }
}