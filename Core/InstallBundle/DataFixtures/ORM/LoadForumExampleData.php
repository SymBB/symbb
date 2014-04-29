<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\InstallBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadForumExampleData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    
    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        
        /*
        $forum = new \SymBB\Core\ForumBundle\Entity\Forum();
        $forum->setActive(1);
        $forum->setName('Main Category');
        $forum->setDescription('This is a Example Category');
        $forum->setPosition(1);
        $forum->setType('category');
        $forum->setShowSubForumList(0);
        
        $forum2 = new \SymBB\Core\ForumBundle\Entity\Forum();
        $forum2->setActive(1);
        $forum2->setName('Sub Forum 1 of Main Category');
        $forum2->setDescription('This is a Example Forum at Position 1');
        $forum2->setPosition(1);
        $forum2->setParent($forum);
        $forum2->setType('forum');
        $forum2->setShowSubForumList(1);
        
        $forum3 = new \SymBB\Core\ForumBundle\Entity\Forum();
        $forum3->setActive(1);
        $forum3->setName('Sub Forum 2 of Main Category');
        $forum3->setDescription('This is a Example Forum at Position 2');
        $forum3->setPosition(2);
        $forum3->setParent($forum);
        $forum3->setType('forum');
        $forum3->setShowSubForumList(1);
        
        $forum4 = new \SymBB\Core\ForumBundle\Entity\Forum();
        $forum4->setActive(1);
        $forum4->setName('Main Forum');
        $forum4->setDescription('This is a Example Main Forum');
        $forum4->setPosition(1);
        $forum4->setType('forum');
        $forum4->setShowSubForumList(1);
        
        $forum5 = new \SymBB\Core\ForumBundle\Entity\Forum();
        $forum5->setActive(1);
        $forum5->setName('Sub Forum 1 of Main Forum');
        $forum5->setDescription('This is a Example Forum at Position 1');
        $forum5->setPosition(1);
        $forum5->setParent($forum4);
        $forum5->setType('forum');
        $forum5->setShowSubForumList(1);
        
        $forum6 = new \SymBB\Core\ForumBundle\Entity\Forum();
        $forum6->setActive(1);
        $forum6->setName('Sub Forum 2 of Main Forum');
        $forum6->setDescription('This is a Example Forum at Position 2');
        $forum6->setPosition(2);
        $forum6->setParent($forum4);
        $forum6->setType('forum');
        $forum6->setShowSubForumList(1);

        $manager->persist($forum);
        $manager->persist($forum2);
        $manager->persist($forum3);
        $manager->persist($forum4);
        $manager->persist($forum5);
        $manager->persist($forum6);
        $manager->flush();
        
        
        
        
        $accessService  = $this->container->get('symbb.core.access.manager');
        
        //GUEST Access
        $accessService->grantAccess(array(
            'SYMBB_FORUM#VIEW',
            'SYMBB_FORUM_RATING#VIEW'
        ), $forum, $this->getReference('guest-group'));
        
        $accessService->grantAccess(array(
            'SYMBB_FORUM#VIEW',
            'SYMBB_FORUM_RATING#VIEW'
        ), $forum2, $this->getReference('guest-group'));
        
        $accessService->grantAccess(array(
            'SYMBB_FORUM#VIEW',
            'SYMBB_FORUM_RATING#VIEW'
        ), $forum3, $this->getReference('guest-group'));
        
        $accessService->grantAccess(array(
            'SYMBB_FORUM#VIEW',
            'SYMBB_FORUM_RATING#VIEW'
        ), $forum4, $this->getReference('guest-group'));
        
        $accessService->grantAccess(array(
            'SYMBB_FORUM#VIEW',
            'SYMBB_FORUM_RATING#VIEW'
        ), $forum5, $this->getReference('guest-group'));
        
        $accessService->grantAccess(array(
            'SYMBB_FORUM#VIEW',
            'SYMBB_FORUM_RATING#VIEW'
        ), $forum6, $this->getReference('guest-group'));
        
        
        // USER ACCESS
        $accessService->grantAccess(array(
            'SYMBB_FORUM#VIEW',
            'SYMBB_FORUM#CREATE_TOPIC',
            'SYMBB_FORUM#CREATE_POST',
            'SYMBB_FORUM_RATING#VIEW',
            'SYMBB_FORUM_RATING#EDIT'
        ), $forum, $this->getReference('user-group'));
        
        $accessService->grantAccess(array(
            'SYMBB_FORUM#VIEW',
            'SYMBB_FORUM#CREATE_TOPIC',
            'SYMBB_FORUM#CREATE_POST',
            'SYMBB_FORUM_RATING#VIEW',
            'SYMBB_FORUM_RATING#EDIT'
        ), $forum2, $this->getReference('user-group'));
        
        $accessService->grantAccess(array(
            'SYMBB_FORUM#VIEW',
            'SYMBB_FORUM#CREATE_TOPIC',
            'SYMBB_FORUM#CREATE_POST',
            'SYMBB_FORUM_RATING#VIEW',
            'SYMBB_FORUM_RATING#EDIT'
        ), $forum3, $this->getReference('user-group'));
        
        $accessService->grantAccess(array(
            'SYMBB_FORUM#VIEW',
            'SYMBB_FORUM#CREATE_TOPIC',
            'SYMBB_FORUM#CREATE_POST',
            'SYMBB_FORUM_RATING#VIEW',
            'SYMBB_FORUM_RATING#EDIT'
        ), $forum4, $this->getReference('user-group'));
        
        $accessService->grantAccess(array(
            'SYMBB_FORUM#VIEW',
            'SYMBB_FORUM#CREATE_TOPIC',
            'SYMBB_FORUM#CREATE_POST',
            'SYMBB_FORUM_RATING#VIEW',
            'SYMBB_FORUM_RATING#EDIT'
        ), $forum5, $this->getReference('user-group'));
        
        $accessService->grantAccess(array(
            'SYMBB_FORUM#VIEW',
            'SYMBB_FORUM#CREATE_TOPIC',
            'SYMBB_FORUM#CREATE_POST',
            'SYMBB_FORUM_RATING#VIEW',
            'SYMBB_FORUM_RATING#EDIT'
        ), $forum6, $this->getReference('user-group'));
        
        
        // ADMIN ACCESS
        $accessService->grantAccess(array(
            'SYMBB_FORUM#VIEW',
            'SYMBB_FORUM#CREATE_TOPIC',
            'SYMBB_FORUM#CREATE_POST',
            'SYMBB_FORUM_MOD#EDIT_TOPIC',
            'SYMBB_FORUM_MOD#EDIT_POST',
            'SYMBB_FORUM_MOD#DELETE_TOPIC',
            'SYMBB_FORUM_MOD#DELETE_POST',
            'SYMBB_FORUM_RATING#VIEW',
            'SYMBB_FORUM_RATING#EDIT'
        ), $forum, $this->getReference('admin-group'));
        
        $accessService->grantAccess(array(
            'SYMBB_FORUM#VIEW',
            'SYMBB_FORUM#CREATE_TOPIC',
            'SYMBB_FORUM#CREATE_POST',
            'SYMBB_FORUM_MOD#EDIT_TOPIC',
            'SYMBB_FORUM_MOD#EDIT_POST',
            'SYMBB_FORUM_MOD#DELETE_TOPIC',
            'SYMBB_FORUM_MOD#DELETE_POST',
            'SYMBB_FORUM_RATING#VIEW',
            'SYMBB_FORUM_RATING#EDIT'
        ), $forum2, $this->getReference('admin-group'));
        
        $accessService->grantAccess(array(
            'SYMBB_FORUM#VIEW',
            'SYMBB_FORUM#CREATE_TOPIC',
            'SYMBB_FORUM#CREATE_POST',
            'SYMBB_FORUM_MOD#EDIT_TOPIC',
            'SYMBB_FORUM_MOD#EDIT_POST',
            'SYMBB_FORUM_MOD#DELETE_TOPIC',
            'SYMBB_FORUM_MOD#DELETE_POST',
            'SYMBB_FORUM_RATING#VIEW',
            'SYMBB_FORUM_RATING#EDIT'
        ), $forum3, $this->getReference('admin-group'));
        
        $accessService->grantAccess(array(
            'SYMBB_FORUM#VIEW',
            'SYMBB_FORUM#CREATE_TOPIC',
            'SYMBB_FORUM#CREATE_POST',
            'SYMBB_FORUM_MOD#EDIT_TOPIC',
            'SYMBB_FORUM_MOD#EDIT_POST',
            'SYMBB_FORUM_MOD#DELETE_TOPIC',
            'SYMBB_FORUM_MOD#DELETE_POST',
            'SYMBB_FORUM_RATING#VIEW',
            'SYMBB_FORUM_RATING#EDIT'
        ), $forum4, $this->getReference('admin-group'));
        
        $accessService->grantAccess(array(
            'SYMBB_FORUM#VIEW',
            'SYMBB_FORUM#CREATE_TOPIC',
            'SYMBB_FORUM#CREATE_POST',
            'SYMBB_FORUM_MOD#EDIT_TOPIC',
            'SYMBB_FORUM_MOD#EDIT_POST',
            'SYMBB_FORUM_MOD#DELETE_TOPIC',
            'SYMBB_FORUM_MOD#DELETE_POST',
            'SYMBB_FORUM_RATING#VIEW',
            'SYMBB_FORUM_RATING#EDIT'
        ), $forum5, $this->getReference('admin-group'));
        
        $accessService->grantAccess(array(
            'SYMBB_FORUM#VIEW',
            'SYMBB_FORUM#CREATE_TOPIC',
            'SYMBB_FORUM#CREATE_POST',
            'SYMBB_FORUM_MOD#EDIT_TOPIC',
            'SYMBB_FORUM_MOD#EDIT_POST',
            'SYMBB_FORUM_MOD#DELETE_TOPIC',
            'SYMBB_FORUM_MOD#DELETE_POST',
            'SYMBB_FORUM_RATING#VIEW',
            'SYMBB_FORUM_RATING#EDIT'
        ), $forum6, $this->getReference('admin-group'));
        */
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 4; // the order in which fixtures will be loaded
    }
}