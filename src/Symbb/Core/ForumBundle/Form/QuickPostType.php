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

class QuickPostType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('text', 'textarea', array('attr' => array('placeholder' => 'Give Your text here', "class" => "symbb-editable")));
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
        return 'quick_post';

    }
}