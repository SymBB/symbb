<?php

/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Extension\PostUploadBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FileType extends AbstractType
{


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '\SymBB\Core\ForumBundle\Entity\Post\File',
            'translation_domain' => 'symbb_frontend'
        ));

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('image', 'file', array('required' => false));

    }

    public function getName()
    {
        return 'post_file';

    }
}