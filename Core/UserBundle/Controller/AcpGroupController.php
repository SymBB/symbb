<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\UserBundle\Controller;

class AcpGroupController extends \SymBB\Core\AdminBundle\Controller\Base\CrudController
{

    protected $entityBundle = 'SymBBCoreUserBundle';

    protected $entityName = 'Group';

    protected $formClass = '\SymBB\Core\UserBundle\Form\Type\Group';


    protected function getForm()
    {
        $entity = $this->getFormEntity();
        $form = $this->createForm(new $this->formClass($this->get('translator'), $this->getRepository()), $entity);
        return $form;

    }

    protected function findListEntities($parent = null)
    {
        $entityList = $this->getRepository()->findBy(array(), array('name' => 'ASC'));
        return $entityList;

    }
    
    protected function checkIsObjectRemoveable($entity, $parent, &$errorMessage){
        $roles = $entity->getRoles();
        if(\in_array('ROLE_GUEST', $roles) || \in_array('ROLE_ADMIN', $roles) || $entity->getType() === "guest"){
            $errorMessage= $this->get('translator')->trans('You you can not delete guest or administrator role', array(), 'symbb_backend');
            return false;
        }
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
            }
            else {
                // new form, return empty entity
                $entity_class_name = $repository->getClassName();
                $entity = new $entity_class_name('');
                $entity->addRole('ROLE_GUEST');
                $entity->addRole('ROLE_USER');
            }

            $this->formEntity = $entity;
        }

        return $this->formEntity;

    }
}