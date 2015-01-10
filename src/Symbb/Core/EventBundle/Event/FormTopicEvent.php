<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\EventBundle\Event;

use Symbb\Core\ForumBundle\DependencyInjection\TopicManager;
use Symfony\Component\EventDispatcher\Event;
use \Symbb\Core\ForumBundle\Entity\Topic;
use Symfony\Component\Form\FormBuilderInterface;
use \Symbb\Core\UserBundle\Manager\UserManager;
use \Symbb\Core\UserBundle\Manager\GroupManager;

class FormTopicEvent extends Event
{


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
     * @var TopicManager
     */
    protected $topicManager;

    /**
     * @var \Symbb\Core\UserBundle\Manager\UserManager
     */
    protected $userManager;

    /**
     *
     * @var \Symbb\Core\UserBundle\Manager\GroupManager
     */
    protected $groupManager;

    public function __construct(FormBuilderInterface $builder, $translator, TopicManager $topicManager, UserManager $userManager, GroupManager $groupManager)
    {
        $this->builder = $builder;
        $this->translator = $translator;
        $this->topicManager = $topicManager;
        $this->userManager = $userManager;
        $this->groupManager = $groupManager;
    }

    /**
     * @return FormBuilderInterface
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @return \Symfony\Component\Translation\TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @return TopicManager
     */
    public function getTopicManager()
    {
        return $this->topicManager;
    }

    /**
     * @return UserManager
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    /**
     * @return GroupManager
     */
    public function getGroupManager()
    {
        return $this->groupManager;
    }

}
