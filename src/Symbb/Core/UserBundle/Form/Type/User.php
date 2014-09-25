<?php
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

class User extends AbstractType
{

    protected $dispatcher;

    /**
     *
     * @var \SymBB\Core\UserBundle\DependencyInjection\UserManager
     */
    protected $usermanager;

    public function __construct($dispatcher, $usermanager)
    {
        $this->dispatcher = $dispatcher;
        $this->usermanager = $usermanager;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '\SymBB\Core\UserBundle\Entity\User',
            'translation_domain' => 'symbb_backend',
            'cascade_validation' => true,
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', "text", array('required' => true))
            ->add(
                'email', "email", array(
                    'required' => true,
                    'constraints' => array(
                        new \Symfony\Component\Validator\Constraints\Email(
                            array(
                            'checkMX' => true,
                            'message' => 'The email "{{ value }}" is not a valid email.'
                            )
                        )
                    )
                )
            )
            ->add('enabled', "checkbox", array('required' => false))
            ->add('plain_password', "repeated", array(
                'type' => 'password',
                'invalid_message' => 'The password fields must match.',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => false,
                'first_options' => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
                'constraints' => $this->usermanager->getPasswordValidatorConstraints()
            ))
            ->add('groups');

        $builderEvent = new \SymBB\Core\EventBundle\Event\BaseFormbuilderEvent($builder);
        $this->dispatcher->dispatch('symbb.core.user.acp.form', $builderEvent);
    }

    public function getName()
    {
        return 'acp_user';
    }
}