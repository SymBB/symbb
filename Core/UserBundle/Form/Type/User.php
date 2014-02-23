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
        
    public function __construct($dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '\SymBB\Core\UserBundle\Entity\User',
            'translation_domain' => 'symbb_backend'
        ));

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', "text", array('required' => true))
            ->add('email', "email", array('required' => true))
            ->add('plain_password', "repeated", array(
                'type' => 'password',
                'invalid_message' => 'The password fields must match.',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => true,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
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