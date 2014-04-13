<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\AdminBundle\Controller\Base;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class CrudController extends Controller
{

    protected $formEntity;

    protected $entityBundle = 'SymBBCoreForumBundle';

    protected $templateBundle = null;

    protected $entityName = '';

    protected $formClass = '';

    public function listAction($parent = null)
    {
        $entityList = $this->findListEntities($parent);

        $params = array('entityList' => $entityList, 'breadcrum' => $this->getBreadcrum($parent), 'parent' => $parent);
        $params = $this->addListParams($params, $parent);
        return $this->render(
            $this->getTemplateBundleName() . ':Acp/' . $this->entityName . ':list.html.twig', $params
        );
    }

    public function getBreadcrum($parent = null)
    {
        $breadcrum = false;
        $path = '_' . $this->entityBundle . '_' . $this->entityName;
        $path = strtolower($path);

        if ($parent) {
            $repository = $this->getRepository();
            $parentEntity = $repository->find($parent);
            if (is_object($parentEntity)) {
                $breadcrum = array();
                $names = $parentEntity->getExtendNameArray();
                $uri = $this->get('router')->generate($path . '_list');
                $breadcrum[] = '<li><a href="' . $uri . '">' . $this->get('translator')->trans('Übersicht', array(), 'symbb_backend') . '</a></li>';
                foreach ($names as $id => $name) {
                    $uri = $this->get('router')->generate($path . '_list_child', array('parent' => $id));
                    $breadcrum[] = '<li><a href="' . $uri . '">' . $name . '</a></li>';
                }
                $breadcrum = implode('', $breadcrum);
            }
        }
        return '<ol class="breadcrumb">' . $breadcrum . '</ol>';
    }

    public function sortAction()
    {
        $request = $this->getRequest();
        $return = array('success' => 0);
        if ($request->isMethod('POST')) {
            $repository = $this->getRepository();
            $em = $this->get('doctrine')->getManager('symbb');
            $entries = (array) $request->get('entry');
            $i = 0;
            foreach ($entries as $entry) {
                $entityId = (int) $entry;
                $entity = $repository->findOneById($entityId);
                if ($entity) {
                    $entity->setPosition($i);
                    $em->persist($entity);
                    $i++;
                }
            }
            $em->flush();
            $return['success'] = 1;
        }
        $json = json_encode($return);
        $response = new \Symfony\Component\HttpFoundation\Response($json);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function newAction()
    {
        return $this->editAction();
    }

    public function editAction()
    {
        $request = $this->getRequest();
        if ($request->isMethod('POST')) {
            return $this->saveAction();
        } else {
            $form = $this->getForm();
            $entity = $this->getFormEntity();
            return $this->editCallback($form, $entity);
        }
    }

    public function saveAction()
    {
        $request = $this->getRequest();
        $form = $this->getForm();

        if ($request->isMethod('POST')) {
            $form->bind($request);
            $entity = $this->getFormEntity();
            if ($form->isValid()) {
                $em = $this->get('doctrine')->getManager('symbb');
                $em->persist($entity);
                $em->flush();
                $parent = null;
                if ($form->has('parent')) {
                    $parent = $form->get('parent')->getData();
                    if (\is_object($parent)) {
                        $parent = $parent->getId();
                    }
                }
                return $this->listAction($parent);
            } else {
                return $this->editCallback($form, $entity);
            }
        }
    }

    public function removeAction($id)
    {
        $repository = $this->getRepository();
        $entity = $repository->findOneById($id);
        $parent = null;
        if (is_object($entity)) {
            if (\method_exists($entity, "getParent")) {
                $parent = $entity->getParent();
            }
            $errorMessage = '';
            if($this->checkIsObjectRemoveable($entity, $parent, $errorMessage)){
                $em = $this->get('doctrine')->getManager('symbb');
                $em->remove($entity);
                $em->flush();
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    $errorMessage
                );
                return $this->listAction(null);
            }
        }
        return $this->listAction($parent);
    }
    
    protected function checkIsObjectRemoveable($entity, $parent, &$errorMessage){
        return true;
    }

    /**
     * Entity object for the form
     * Dont load the object twice and load from this method
     *
     * @return Object
     */
    protected function getFormEntity()
    {
        if ($this->formEntity === null) {
            $request = $this->getRequest();
            $entityId = (int) $request->get('id');
            $repository = $this->getRepository();

            if ($entityId > 0) {
                // edit form
                $entity = $repository->findOneById($entityId);
            } else {
                // new form, return empty entity
                $entity_class_name = $repository->getClassName();
                $entity = new $entity_class_name();
            }

            $this->formEntity = $entity;
        }

        return $this->formEntity;
    }

    protected function getForm()
    {
        $entity = $this->getFormEntity();
        $form = $this->createForm(new $this->formClass, $entity);
        return $form;
    }

    protected function addListParams($params, $parent = null)
    {
        return $params;
    }

    protected function addFormParams($params, $form, $entity)
    {
        return $params;
    }

    protected function getRepository()
    {
        $repo = $this->get('doctrine')->getRepository($this->entityBundle . ':' . $this->entityName, 'symbb');
        return $repo;
    }

    protected function findListEntities($parent = null)
    {
        if ($parent === null) {
            $entityList = $this->getRepository()->findAll();
        } else {
            $entityList = $this->getRepository()->findBy(array('parent' => $parent));
        }
        return $entityList;
    }

    protected function editCallback($form, $entity)
    {
        $parent = null;
        
        if (\method_exists($entity, "getParent")) {
            $parent = $entity->getParent();
        }
        
        $params = array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrum' => $this->getBreadcrum($parent)
        );

        $params = $this->addFormParams($params, $form, $entity);

        return $this->render($this->getTemplateBundleName() . ':Acp/' . $this->entityName . ':edit.html.twig', $params);
    }

    protected function getTemplateBundleName()
    {
        if ($this->templateBundle === null) {
            $manager = $this->container->get('symbb.core.site.manager');
            $this->templateBundle = $manager->getTemplate('acp');
        }
        return $this->templateBundle;
    }
}