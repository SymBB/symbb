<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \SymBB\Extension\BBCodeBundle\Form\Type\BBEditorType;

class Option extends AbstractType
{


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain'    => 'symbb_frontend'
        ));

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('avatar', 'text', array('required' => false ,'attr' => array('placeholder' => 'http://deine-avatar.url')))
                ->add('signature', 'textarea', array('required' => false, 'attr' => array()))
                ->add('timezone', 'timezone', array('required' => true))
                ->add('save', 'submit', array('attr' => array('class' => 'btn-success')));

    }


    public function getName()
    {
        return 'user_options';

    }
    
}