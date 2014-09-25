<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SiteBundle\Controller;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class AcpNavigationItemController extends \Symbb\Core\AdminBundle\Controller\Base\CrudController
{

    protected $entityBundle = 'SymbbCoreSiteBundle';

    protected $entityName = 'Navigation\Item';

    protected $formClass = '\Symbb\Core\SiteBundle\Form\Type\NavigationItem';

    protected $templateDirectory = 'Site/Navigation/Item';


    public function newItemAction($navigation, $parent = 0, Request $request)
    {

        $entity = $this->getFormEntity($request);

        if($navigation){
            $repo = $this->get('doctrine')->getRepository($this->entityBundle . ':Navigation', $this->entityManagerName);
            $navi = $repo->find($navigation);
            $entity->setNavigation($navi);
        }

        if ($parent) {
            $repository = $this->getRepository();
            $parent = $repository->findOneById($parent);
            $parent = $repository->findOneById($parent);
            $entity->setParentItem($parent);
        }

        return parent::newAction($request, $parent);
    }

    protected function findListEntities($parent = null)
    {
        $entityList = $this->getRepository()->findBy(array('parentItem' => $parent), array('position' => 'ASC'));
        return $entityList;
    }

    protected function getForm(Request $request)
    {
        $entity = $this->getFormEntity($request);
        $form = $this->createForm(new $this->formClass($this->get('router')), $entity);
        return $form;
    }

    protected function beforeSaveFlush(Request $request, Form $form, $entity){

        $data = $request->get('navigation_item');
        $params = array();
        foreach($data as $key => $value){
            if(strpos($key, 'symfonyRouteParam_') === 0){
                $key = str_replace('symfonyRouteParam_', '', $key);
                $params[$key] = $value;
            }
        }
        $entity->setSymfonyRouteParams($params);
    }

    public function listAction($parent = null)
    {
        return $this->render(
            $this->getTemplateBundleName() . ':Acp/Site/Navigation:redirectToList.html.twig', array()
        );
    }
}