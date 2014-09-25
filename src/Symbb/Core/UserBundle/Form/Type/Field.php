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

class Field extends AbstractType
{



    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '\SymBB\Core\UserBundle\Entity\Field',
            'translation_domain' => 'symbb_backend'
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', "text", array('required' => true))
            ->add('dataType', "choice", array('required' => true, 'choices' => array('string' => 'short Text', 'text' => 'long Text', 'boolean' => 'Yes/No')))
            ->add('displayInForum', "checkbox", array('required' => false))
            ->add('displayInMemberlist', "checkbox", array('required' => false));
    }

    public function getName()
    {
        return 'acp_user';
    }
}