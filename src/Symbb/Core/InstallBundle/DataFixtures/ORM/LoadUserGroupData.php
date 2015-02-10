<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Symbb\Core\InstallBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserGroupData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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

        $guestName = $this->container->get('translator')->trans('Guest');
        $adminName = $this->container->get('translator')->trans('Admin');
        $userName = $this->container->get('translator')->trans('User');


        $groupManager = $this->container->get('fos_user.group_manager');

        $groupGuest = $groupManager->createGroup($guestName);
        $groupGuest->setType('guest');
        $groupGuest->addRole('ROLE_GUEST');

        $groupUser = $groupManager->createGroup($userName);
        $groupUser->setType('user');
        $groupUser->addRole('ROLE_GUEST');
        $groupUser->addRole('ROLE_USER');

        $groupAdmin = $groupManager->createGroup($adminName);
        $groupAdmin->setType('admin');
        $groupAdmin->addRole('ROLE_GUEST');
        $groupAdmin->addRole('ROLE_USER');
        $groupAdmin->addRole('ROLE_ADMIN');


        $groupManager->updateGroup($groupGuest);
        $groupManager->updateGroup($groupUser);
        $groupManager->updateGroup($groupAdmin);

        $this->addReference('guest-group', $groupGuest);
        $this->addReference('user-group', $groupUser);
        $this->addReference('admin-group', $groupAdmin);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
}