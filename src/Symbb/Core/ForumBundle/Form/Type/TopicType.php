<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Form\Type;

use Symbb\Core\EventBundle\Event\FormTopicEvent;
use Symbb\Core\ForumBundle\DependencyInjection\TopicManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \Symbb\Core\UserBundle\Manager\UserManager;
use \Symbb\Core\UserBundle\Manager\GroupManager;

class TopicType extends AbstractType
{


    /**
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcher 
     */
    protected $dispatcher;

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

    public function setDispatcher($object){
        $this->dispatcher = $object;
    }

    public function setTranslator($object){
        $this->translator = $object;
    }

    /**
     * @param UserManager $object
     */
    public function setUserManager(UserManager $object){
        $this->userManager = $object;
    }

    /**
     * @param GroupManager $object
     */
    public function setGroupManager(GroupManager $object){
        $this->groupManager = $object;
    }

    /**
     * @param TopicManager $object
     */
    public function setTopicManager(TopicManager $object){
        $this->topicManager = $object;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tags =  $this->topicManager->getAvailableTags();

        $builder
            ->add('name', 'text', array('label' => 'Titel', 'required' => true, 'attr' => array('placeholder' => 'Enter a name here')))
            ->add('mainPost', "post")
            ->add('tags', 'choice', array(
                "choices" => $tags,
                'required'  => false
            ))
            ->add('locked', 'checkbox', array('required' => false, 'label' => 'close topic'))
            ->add('id', 'hidden')
            ->add('forum', 'entity', array('class' => 'SymbbCoreForumBundle:Forum', 'disabled' => true));

        // create Event to manipulate Post Form
        $event = new FormTopicEvent($builder, $this->translator, $this->topicManager, $this->userManager, $this->groupManager);
        $this->dispatcher->dispatch('symbb.topic.controller.form', $event);

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Symbb\Core\ForumBundle\Entity\Topic',
            'translation_domain' => 'symbb_frontend',
            'cascade_validation' => true
        ));

    }

    public function getName()
    {
        return 'topic';

    }
}