<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\ForumBundle\Controller;

use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use SymBB\Core\InstallBundle\DataFixtures\ORM\LoadTopicTags;
use Symfony\Component\HttpFoundation\Request;

class AcpTopicTagController extends \SymBB\Core\AdminBundle\Controller\Base\CrudController
{

    protected $entityBundle = 'SymBBCoreForumBundle';

    protected $entityName = 'Topic\Tag';

    protected $formClass = '\SymBB\Core\ForumBundle\Form\Type\TopicTag';

    protected function getForm(Request $request)
    {
        $entity = $this->getFormEntity($request);
        $form = $this->createForm(new $this->formClass($this->get('translator'), $this->get('doctrine.orm.symbb_entity_manager')), $entity);
        return $form;
    }

    protected function findListEntities($parent = null)
    {
        $entityList = $this->getRepository()->findBy(array(), array('priority' => 'DESC'));
        return $entityList;
    }

    protected function getTemplateDirectory()
    {
        return 'Forum/TopicTag';
    }

    public function restoreAction()
    {

        $em = $this->get('doctrine.orm.symbb_entity_manager');
        $sets = $em->getRepository('SymBBCoreForumBundle:Topic\Tag')->findAll();
        foreach ($sets as $set) {
            $em->remove($set);
        }
        $em->flush();

        //load fixture
        $loader = new Loader();
        $loader->addFixture(new LoadTopicTags());

        $purger = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures(), true);

        return $this->listAction();
    }
}