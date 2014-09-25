<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \Symbb\Core\UserBundle\DependencyInjection\UserManager;
use \Symbb\Core\UserBundle\DependencyInjection\GroupManager;

class TopicType extends AbstractType
{

    protected $url;

    /**
     * @var \Symbb\Core\ForumBundle\Entity\Topic
     */
    protected $topic;

    /**
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcher 
     */
    protected $dispatcher;

    /**
     *
     * @var \Symfony\Component\Translation\Translator 
     */
    protected $translator;

    /**
     *
     * @var \Doctrine\ORM\EntityManager 
     */
    protected $em;

    /**
     * @var \Symbb\Core\UserBundle\DependencyInjection\UserManager
     */
    protected $userManager;

    /**
     *
     * @var \Symbb\Core\UserBundle\DependencyInjection\GroupManager
     */
    protected $groupManager;

    public function __construct($url, \Symbb\Core\ForumBundle\Entity\Topic $topic, $dispatcher, $translator, $em, UserManager $userManager, GroupManager $groupManager)
    {
        $this->url = $url;
        $this->topic = $topic;
        $this->dispatcher = $dispatcher;
        $this->translator = $translator;
        $this->em = $em;
        $this->userManager = $userManager;
        $this->groupManager = $groupManager;

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $postType = new PostType('', $this->topic->getMainPost(), $this->dispatcher, $this->translator, $this->em, $this->userManager, $this->groupManager);
        $builder
            ->add('name', 'text', array('label' => 'Titel', 'required' => true, 'attr' => array('placeholder' => 'Enter a name here')))
            ->add('mainPost', $postType)
            ->add('locked', 'checkbox', array('required' => false, 'label' => 'close topic'))
            ->add('id', 'hidden')
            ->add('forum', 'entity', array('class' => 'SymbbCoreForumBundle:Forum', 'disabled' => true))
            ->setAction($this->url);

        // create Event to manipulate Post Form
        $event = new \Symbb\Core\EventBundle\Event\FormTopicEvent($this->topic, $builder, $this->translator, $this->em, $this->userManager, $this->groupManager);
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