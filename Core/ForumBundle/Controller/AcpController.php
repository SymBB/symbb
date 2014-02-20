<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\ForumBundle\Controller;

class AcpController extends \SymBB\Core\AdminBundle\Controller\Base\CrudController
{

    protected $entityBundle = 'SymBBCoreForumBundle';

    protected $entityName = 'Forum';

    protected $formClass = '\SymBB\Core\ForumBundle\Form\Type\Forum';

    public function newCategoryAction($parent = 0)
    {
        $entity = $this->getFormEntity();
        $entity->setType('category');

        if ($parent) {
            $repository = $this->getRepository();
            $parent = $repository->findOneById($parent);
            $entity->setParent($parent);
        }

        return $this->editAction();
    }

    public function newLinkAction($parent = 0)
    {
        $entity = $this->getFormEntity();
        $entity->setType('link');

        if ($parent) {
            $repository = $this->getRepository();
            $parent = $repository->findOneById($parent);
            $entity->setParent($parent);
        }

        return $this->editAction($parent);
    }

    public function newAction($parent = 0)
    {
        if ($parent) {
            $entity = $this->getFormEntity();
            $repository = $this->getRepository();
            $parent = $repository->findOneById($parent);
            $entity->setParent($parent);
        }

        return parent::newAction();
    }

    protected function getForm()
    {
        $entity = $this->getFormEntity();
        $form = $this->createForm(new $this->formClass($this->get('translator'), $this->getRepository()), $entity);
        return $form;
    }

    protected function findListEntities($parent = null)
    {
        $entityList = $this->getRepository()->findBy(array('parent' => $parent), array('position' => 'ASC'));
        return $entityList;
    }

    protected function addListParams($params, $parent = null)
    {
        $formatType = new \SymBB\Core\ForumBundle\Helper\Format\Forum\Type();
        $formatType->setTranslator($this->get('translator'));
        $params['helper']['type'] = $formatType;

        if ($parent) {
            $params['parent'] = $parent;
        } else {
            $params['parent'] = 0;
        }
        
        $allEntries = $this->findListEntities(null);
        $params['allEntries'] = $allEntries;
        
        return $params;
    }

    protected function addFormParams($params, $form, $entity)
    {
        $parent = $entity->getParent();
        if ($parent) {
            $params['parent'] = $parent->getId();
        } else {
            $params['parent'] = 0;
        }
        return $params;
    }
}