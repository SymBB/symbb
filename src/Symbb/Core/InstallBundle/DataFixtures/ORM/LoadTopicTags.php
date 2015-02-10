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
use Symbb\Core\ForumBundle\Entity\Topic\Tag;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadTopicTags extends AbstractFixture
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $tag1 = new Tag();
        $tag1->setId('announcement');
        $tag1->setName('Announcement');
        $tag1->setPriority(2);

        $tag2 = new Tag();
        $tag2->setId('important');
        $tag2->setName('Important');
        $tag2->setPriority(1);

        $manager->persist($tag1);
        $manager->persist($tag2);

        $manager->flush();

    }
}