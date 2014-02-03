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
use \SymBB\Core\UserBundle\DependencyInjection\UserManager;
use \SymBB\Core\UserBundle\DependencyInjection\GroupManager;

class PostType extends AbstractType
{

    protected $url;

    /**
     * @var \SymBB\Core\ForumBundle\Entity\Post 
     */
    protected $post;

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
     * @var \SymBB\Core\UserBundle\DependencyInjection\UserManager 
     */
    protected $userManager;

    /**
     *
     * @var \SymBB\Core\UserBundle\DependencyInjection\GroupManager 
     */
    protected $groupManager;
    

    public function __construct($url, \SymBB\Core\ForumBundle\Entity\Post $post, $dispatcher, $translator, $em, UserManager $userManager, GroupManager $groupManager)
    {
        $this->url = $url;
        $this->post = $post;
        $this->dispatcher = $dispatcher;
        $this->translator = $translator;
        $this->em = $em;
        $this->userManager = $userManager;
        $this->groupManager = $groupManager;

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('text', 'textarea', array('attr' => array('placeholder' => 'Give Your text here')));
        $builder->add('notifyMe', 'checkbox', array("mapped" => false, 'required' => false));
        $builder->add('id', 'hidden')
            ->setAction($this->url);


        // create Event to manipulate Post Form
        $event = new \SymBB\Core\EventBundle\Event\FormPostEvent($this->post, $builder, $this->translator, $this->em, $this->userManager, $this->groupManager);
        $this->dispatcher->dispatch('symbb.post.controller.form', $event);
        //

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SymBB\Core\ForumBundle\Entity\Post',
            'translation_domain' => 'symbb_frontend',
            'cascade_validation' => true
        ));

    }

    public function getName()
    {
        return 'post';

    }
}