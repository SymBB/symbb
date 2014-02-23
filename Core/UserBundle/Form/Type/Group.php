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

class Group extends AbstractType
{


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain'    => 'symbb_frontend'
        ));

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('name', 'text', array('required' => true ,'attr' => array('placeholder' => 'Name of the Group')))
                ;
    }


    public function getName()
    {
        return 'user_groups';

    }
}