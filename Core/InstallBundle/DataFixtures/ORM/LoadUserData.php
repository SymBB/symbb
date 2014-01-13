<?
/**
*
* @package symBB
* @copyright (c) 2013 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\InstallBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        
        $guestName      = $this->container->get('translator')->trans('Guest');
        $userName       = $this->container->get('translator')->trans('User');
        $adminName      = $this->container->get('translator')->trans('Admin');
        
        $userManager    = $this->container->get('symbb.core.user.manager');
        
        $userGuest      = $userManager->createUser();
        $userGuest->setUsername($guestName);
        $userGuest->setSymbbType('guest');
        $userGuest->addGroup($this->getReference('guest-group'));
        $userGuest->setPlainPassword('guest');
        $userGuest->setEnabled(false);
        $userGuest->setEmail('guest@no-email.com');
        
        $userUser       = $userManager->createUser();
        $userUser->setUsername($userName);
        $userUser->setSymbbType('user');
        $userUser->setPlainPassword('user');
        $userUser->setEmail('user@no-email.com');
        $userUser->setEnabled(true);
        $userUser->addGroup($this->getReference('user-group'));
        
        $userAdmin      = $userManager->createUser();
        $userAdmin->setUsername($adminName);
        $userAdmin->setSymbbType('user'); // admin is also "user" only the group is different
        $userAdmin->setPlainPassword('admin');
        $userAdmin->setEmail('admin-email.com');
        $userAdmin->setEnabled(true);
        $userAdmin->addGroup($this->getReference('admin-group'));
       
        $userManager->updateUser($userGuest);
        $userManager->updateUser($userUser);
        $userManager->updateUser($userAdmin);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }
}