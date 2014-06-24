<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\AdminBundle\Controller\Base;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

abstract class CrudController extends Controller
{

    protected $formEntity;

    protected $entityBundle = 'SymBBCoreForumBundle';

    protected $templateBundle = null;

    protected $entityName = '';

    protected $templateDirectory = '';

    protected $formClass = '';

    protected $entityManagerName = 'symbb';

    protected $parentField = 'parent';

    public function listAction($parent = null)
    {
        $entityList = $this->findListEntities($parent);

        $params = array('entityList' => $entityList, 'breadcrum' => $this->getBreadcrum($parent), $this->parentField => $parent);
        $params = $this->addListParams($params, $parent);
        return $this->render(
                $this->getTemplateBundleName() . ':Acp/' . $this->getTemplateDirectory() . ':list.html.twig', $params
        );
    }

    protected function getTemplateDirectory()
    {
        if(!$this->templateDirectory){
            $this->templateDirectory = $this->entityName;
        }
        return $this->templateDirectory;
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
                    $uri = $this->get('router')->generate($path . '_list_child', array($this->parentField => $id));
                    $breadcrum[] = '<li><a href="' . $uri . '">' . $name . '</a></li>';
                }
                $breadcrum = implode('', $breadcrum);
            }
        }
        return '<ol class="breadcrumb">' . $breadcrum . '</ol>';
    }

    public function sortAction(Request $request)
    {
        $return = array('success' => 0);
        if ($request->isMethod('POST')) {
            $repository = $this->getRepository();
            $em = $this->getEntityManager();
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

    public function newAction(Request $request, $parent = 0)
    {
        return $this->editAction($request, $parent);
    }

    public function editAction(Request $request, $parent = 0)
    {
        $form = $this->getForm($request);

        if ($request->isMethod('POST')) {
            return $this->saveAction($request, $form);
        } else {
            $form->handleRequest($request);
            $entity = $this->getFormEntity($request);
            return $this->editCallback($form, $entity);
        }
    }

    public function saveAction($request, $form)
    {

        if ($request->isMethod('POST')) {
            $entity = $this->getFormEntity($request);
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getEntityManager();
                $this->beforeSaveFlush($request, $form, $entity);
                $em->persist($entity);
                $em->flush();
                $parent = null;
                if ($form->has($this->parentField)) {
                    $parent = $form->get($this->parentField)->getData();
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

    protected function beforeSaveFlush(Request $request, Form $form, $entity){

    }

    public function removeAction($id)
    {
        $repository = $this->getRepository();
        $entity = $repository->findOneById($id);
        $parent = null;
        if (is_object($entity)) {
            $method = "get".ucfirst($this->parentField);
            if (\method_exists($entity, $method)) {
                $parent = $entity->$method();
            }
            $errorMessage = '';
            if ($this->checkIsObjectRemoveable($entity, $parent, $errorMessage)) {
                $em = $this->getEntityManager();
                $em->remove($entity);
                $em->flush();
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error', $errorMessage
                );
                return $this->listAction(null);
            }
        }
        return $this->listAction($parent);
    }

    protected function checkIsObjectRemoveable($entity, $parent, &$errorMessage)
    {
        return true;
    }

    /**
     * Entity object for the form
     * Dont load the object twice and load from this method
     *
     * @return Object
     */
    protected function getFormEntity(Request $request)
    {
        if ($this->formEntity === null) {
            $entityId = $request->get('id');
            $repository = $this->getRepository();
            $entity = null;
            if (!empty($entityId)) {
                $entity = $repository->findOneById($entityId);
            }
            $id = $entity->getId();
            if(!is_object($entity) || empty($id) ){
                // new form, return empty entity
                $entity_class_name = $repository->getClassName();
                $entity = new $entity_class_name();
            }

            $this->formEntity = $entity;
        }

        return $this->formEntity;
    }

    protected function getForm(Request $request)
    {
        $entity = $this->getFormEntity($request);
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
        $repo = $this->get('doctrine')->getRepository($this->entityBundle . ':' . $this->entityName, $this->entityManagerName);
        return $repo;
    }

    protected function getEntityManager($managerName = '')
    {
        if (empty($managerName)) {
            $managerName = $this->entityManagerName;
        }
        $em = $this->get('doctrine')->getManager($managerName);
        return $em;
    }

    protected function findListEntities($parent = null)
    {
        if ($parent === null) {
            $entityList = $this->getRepository()->findAll();
        } else {
            $entityList = $this->getRepository()->findBy(array($this->parentField => $parent));
        }
        return $entityList;
    }

    protected function editCallback($form, $entity)
    {
        $parent = null;

        $method = "get".ucfirst($this->parentField);
        if (\method_exists($entity, $method)) {
            $parent = $entity->$method();
        }

        $params = array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrum' => $this->getBreadcrum($parent)
        );

        $params = $this->addFormParams($params, $form, $entity);

        return $this->render($this->getTemplateBundleName() . ':Acp/' . $this->getTemplateDirectory() . ':edit.html.twig', $params);
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