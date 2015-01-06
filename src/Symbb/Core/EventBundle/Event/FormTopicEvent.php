<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\EventBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use \Symbb\Core\ForumBundle\Entity\Topic;
use Symfony\Component\Form\FormBuilderInterface;
use \Symbb\Core\UserBundle\Manager\UserManager;
use \Symbb\Core\UserBundle\Manager\GroupManager;

class FormTopicEvent extends Event
{

    /**
     * @var Topic 
     */
    protected $topic;

    /**
     *
     * @var FormBuilderInterface 
     */
    protected $builder;

    /**
     *
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     *
     * @var \Doctrine\ORM\EntityManager 
     */
    protected $em;

    /**
     * @var \Symbb\Core\UserBundle\Manager\UserManager
     */
    protected $userManager;

    /**
     *
     * @var \Symbb\Core\UserBundle\Manager\GroupManager
     */
    protected $groupManager;

    public function __construct(Topic $topic, FormBuilderInterface $builder, $translator, $em, UserManager $userManager, GroupManager $groupManager)
    {
        $this->topic = $topic;
        $this->builder = $builder;
        $this->translator = $translator;
        $this->em = $em;
        $this->userManager = $userManager;
        $this->groupManager = $groupManager;

    }

    public function getTopic()
    {
        return $this->topic;

    }

    public function getBuilder()
    {
        return $this->builder;

    }

    /**
     * 
     * @return \Symfony\Component\Translation\TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;

    }

    /**
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;

    }

    /**
     * 
     * @return \Symbb\Core\UserBundle\Manager\GroupManager
     */
    public function getGroupManager()
    {
        return $this->groupManager;

    }

    /**
     * 
     * @return \Symbb\Core\UserBundle\Manager\UserManager
     */
    public function getUserManager()
    {
        return $this->userManager;

    }
}
