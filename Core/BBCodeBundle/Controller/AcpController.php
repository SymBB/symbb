<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\BBCodeBundle\Controller;

use Doctrine\Common\DataFixtures\Loader;
use SymBB\Core\InstallBundle\DataFixtures\ORM\LoadBBCode;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class AcpController extends \SymBB\Core\AdminBundle\Controller\Base\CrudController
{

    protected $entityBundle = 'SymBBCoreBBCodeBundle';

    protected $entityName = 'BBCode';

    protected $formClass = '\SymBB\Core\BBCodeBundle\Form\Type\BBCode';

    protected function getForm()
    {
        $entity = $this->getFormEntity();
        $form = $this->createForm(new $this->formClass($this->get('translator'), $this->get('doctrine.orm.symbb_entity_manager')), $entity);
        return $form;
    }

    protected function findListEntities($parent = null)
    {
        $entityList = $this->getRepository()->findBy(array(), array('position' => 'ASC'));
        return $entityList;
    }

    public function restoreAction()
    {

        $em = $this->get('doctrine.orm.symbb_entity_manager');
        $sets = $em->getRepository('SymBBCoreBBCodeBundle:Set')->findAll();
        foreach ($sets as $set) {
            $em->remove($set);
        }
        $codes = $em->getRepository('SymBBCoreBBCodeBundle:BBCode')->findAll();
        foreach ($codes as $code) {
            $em->remove($code);
        }

        $em->flush();


        //load fixture
        $loader = new Loader();
        $loader->addFixture(new LoadBBCode);

        $purger = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures(), true);

        return $this->listAction();
    }
}