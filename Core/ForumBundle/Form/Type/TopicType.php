<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\ForumBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TopicType extends AbstractType
{
    protected $url;
    
    /**
     * @var \SymBB\Core\ForumBundle\Entity\Topic
     */
    protected $topic;
    protected $dispatcher;
    protected $translator;
    
    public function __construct($url, \SymBB\Core\ForumBundle\Entity\Topic $topic, $dispatcher, $translator) {
        $this->url = $url;
        $this->topic = $topic;
        $this->dispatcher = $dispatcher;
        $this->translator = $translator;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $postType = new PostType('', $this->topic->getMainPost(), $this->dispatcher, $this->translator, false);
        $builder//->add('name', 'text', array('label' => 'Titel', 'required' => true, 'attr' => array('placeholder' => 'Enter a name here')))
                ->add('mainPost', $postType)
                ->add('locked', 'checkbox', array('required'  => false, 'label' => 'close topic'))
                ->add('id', 'hidden')
                ->add('forum', 'entity', array('class' => 'SymBBCoreForumBundle:Forum', 'disabled' => true))
                ->setAction($this->url);
        
        // create Event to manipulate Post Form
        $event      = new \SymBB\Core\EventBundle\Event\FormTopicEvent($this->topic, $builder, $this->translator);
        $this->dispatcher->dispatch('symbb.topic.controller.form', $event);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SymBB\Core\ForumBundle\Entity\Topic',
            'translation_domain' => 'symbb_frontend',
            'cascade_validation' => true
        ));
    }

    public function getName()
    {
        return 'topic';
    }
}