<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Form\Type;

use Symbb\Core\ForumBundle\DependencyInjection\PostManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \Symbb\Core\UserBundle\Manager\UserManager;
use \Symbb\Core\UserBundle\Manager\GroupManager;

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
     * @param PostManager $object
     */
    public function setPostManager(PostManager $object){
        $this->postManager = $object;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('text', 'textarea', array('attr' => array('placeholder' => 'Give Your text here', "class" => "symbb-editable")));
        $builder->add('notifyMe', 'checkbox', array("mapped" => false, 'required' => false, 'label' => 'Notify me'));
        $builder->add('id', 'hidden');


        // create Event to manipulate Post Form
        $event = new \Symbb\Core\EventBundle\Event\FormPostEvent($builder, $this->translator, $this->postManager, $this->userManager, $this->groupManager);
        $this->dispatcher->dispatch('symbb.post.controller.form', $event);
        //

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Symbb\Core\ForumBundle\Entity\Post',
            'translation_domain' => 'symbb_frontend',
            'cascade_validation' => true
        ));

    }

    public function getName()
    {
        return 'post';

    }
}