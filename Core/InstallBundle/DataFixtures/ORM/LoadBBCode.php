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

class LoadBBCode extends AbstractFixture
{
    
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        
        $setDefault = new \SymBB\Core\BBCodeBundle\Entity\Set();
        $setDefault->setName("Default");
        
        $setSignature = new \SymBB\Core\BBCodeBundle\Entity\Set();
        $setSignature->setName("Signature");
        
        $manager->persist($setDefault);
        $manager->persist($setSignature);
        $manager->flush();
        
    }
}