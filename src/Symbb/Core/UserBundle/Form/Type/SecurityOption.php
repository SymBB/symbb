<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SecurityOption extends AbstractType
{

    /**
     *
     * @var \Symbb\Core\UserBundle\DependencyInjection\UserManager
     */
    protected $usermanager;
    
    public function __construct($usermanager)
    {
        $this->usermanager = $usermanager;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain'    => 'symbb_frontend'
        ));

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('password', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'The password fields must match.',
                    'options' => array('attr' => array('class' => 'password-field')),
                    'required' => true,
                    'first_options'  => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password'),
                    'constraints' => $this->usermanager->getPasswordValidatorConstraints()
                ))
                ->add('save', 'submit', array('attr' => array('class' => 'btn-success', 'onclick' => 'submit();')));
    }


    public function getName()
    {
        return 'user_options_security';

    }
    
}