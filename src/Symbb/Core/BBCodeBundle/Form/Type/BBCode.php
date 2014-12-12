<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\BBCodeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BBCode extends AbstractType
{

    protected $translator;

    protected $em;

    public function __construct($translator, $em)
    {
        $this->translator = $translator;
        $this->em = $em;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '\Symbb\Core\BBCodeBundle\Entity\BBCode',
            'translation_domain' => 'symbb_backend'
        ));

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name')
            ->add('sets', 'entity', array(
                'class' => 'SymbbCoreBBCodeBundle:Set',
                'choices' => $this->getParentList(),
                'required' => true,
                'multiple' => true
            ))
            ->add('searchRegex', 'text')
            ->add('replaceRegex', 'text')
            ->add('removeNewLines', 'checkbox')
            ->add('buttonRegex', 'text')
            ->add('jsFunction', 'text')
            ->add('image', 'text');
    }

    private function getParentList()
    {

        $list = array();

        $entries = $this->em->getRepository('SymbbCoreBBCodeBundle:Set')->findAll();

        return $entries;

    }

    public function getName()
    {
        return 'bbcode';

    }
}