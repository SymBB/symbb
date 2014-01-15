<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymBB\FOS\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends \FOS\UserBundle\Form\Type\RegistrationFormType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'symbb_frontend', 'attr' => array('placeholder' => 'Your E-Mail')))
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'symbb_frontend', 'attr' => array('placeholder' => 'Your Username')))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'symbb_frontend'),
                'first_options' => array('label' => 'form.password', 'attr' => array('placeholder' => 'Your Password')),
                'second_options' => array('label' => 'form.password_confirmation', 'attr' => array('placeholder' => 'Retype your Password')),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->add('terms', 'checkbox', array('label' => 'Read and accepted Terms of Use', 'translation_domain' => 'symbb_frontend', 'mapped' => false))
            
        ;
    }

    
    public function getName()
    {
        return 'symbb_fos_user_registration';
    }
}