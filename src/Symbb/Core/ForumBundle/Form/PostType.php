<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Form;

use Symbb\Core\ForumBundle\DependencyInjection\PostManager;
use Symbb\Core\ForumBundle\Entity\Post;
use Symbb\Core\ForumBundle\DependencyInjection\TopicManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \Symbb\Core\UserBundle\Manager\UserManager;
use \Symbb\Core\UserBundle\Manager\GroupManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class PostType extends AbstractType
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
     * @var PostManager
     */
    protected $postManager;

    /**
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

    public function setDispatcher($object)
    {
        $this->dispatcher = $object;
    }

    public function setTranslator($object)
    {
        $this->translator = $object;
    }

    /**
     * @param UserManager $object
     */
    public function setUserManager(UserManager $object)
    {
        $this->userManager = $object;
    }

    /**
     * @param GroupManager $object
     */
    public function setGroupManager(GroupManager $object)
    {
        $this->groupManager = $object;
    }

    /**
     * @param PostManager $object
     */
    public function setPostManager(PostManager $object)
    {
        $this->postManager = $object;
    }

    /**
     * @param PostManager $object
     */
    public function setTopicManager(TopicManager $object)
    {
        $this->topicManager = $object;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', "text", array('label' => 'Titel', 'required' => true, 'attr' => array('placeholder' => 'Enter a name here')));
        $builder->add('text', 'textarea', array('attr' => array('placeholder' => 'Give Your text here', "class" => "symbb-editable")));
        $builder->add('id', 'hidden');

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder) {
            $data = $event->getData();
            /* Check we're looking at the right data/form */
            if ($data instanceof Post) {
                $form = $event->getForm();
                $form->add('notifyMe', 'checkbox', array("mapped" => false, 'required' => false, 'label' => 'Notify me', "data" => $this->topicManager->checkFlag($data->getTopic(), "notify", $this->userManager->getCurrentUser())));
            }
        });

        // create Event to manipulate Post Form
        $event = new \Symbb\Core\EventBundle\Event\FormPostEvent($builder, $this->translator, $this->postManager, $this->userManager, $this->groupManager);
        $this->dispatcher->dispatch('symbb.core.forum.topic.post.create', $event);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Symbb\Core\ForumBundle\Entity\Post',
            'translation_domain' => 'symbb_frontend',
            'cascade_validation' => true,
            'error_bubbling' => true
        ));

    }

    public function getName()
    {
        return 'post';

    }
}