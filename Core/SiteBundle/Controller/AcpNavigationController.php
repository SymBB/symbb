<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class AcpNavigationController extends \SymBB\Core\AdminBundle\Controller\Base\CrudController
{

    protected $entityBundle = 'SymBBCoreSiteBundle';

    protected $entityName = 'Navigation';

    protected $formClass = '\SymBB\Core\SiteBundle\Form\Type\Navigation';

    protected $templateDirectory = 'Site/Navigation';

    protected $parentField = 'site';

    public function newAction(Request $request, $parent = 0)
    {
        $site = $request->get('site');
        $em = $this->getEntityManager();
        $site = $em->getRepository('SymBBCoreSiteBundle:Site')->find($site);

        $entity = $this->getFormEntity($request);
        $entity->setSite($site);

        return $this->editAction($request, $parent);
    }

    public function listAction($site = null)
    {
        return $this->render(
            $this->getTemplateBundleName() . ':Acp/' . $this->getTemplateDirectory() . ':redirectToList.html.twig', array()
        );
    }

}