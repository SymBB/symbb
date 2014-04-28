<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\BBCodeBundle\Form\Type;

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
            'data_class' => '\SymBB\Core\BBCodeBundle\Entity\BBCode',
            'translation_domain' => 'symbb_backend'
        ));

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name')
            ->add('set', 'entity', array(
                'class' => 'SymBBCoreBBCodeBundle:Set',
                'choices' => $this->getParentList(),
                'required' => true
            ))
            ->add('searchRegex', 'text')
            ->add('replaceRegex', 'text')
            ->add('buttonRegex', 'text')
            ->add('image', 'text');
    }

    private function getParentList()
    {

        $list = array();

        $entries = $this->em->getRepository('SymBBCoreBBCodeBundle:Set')->findAll();

        return $entries;

    }

    public function getName()
    {
        return 'bbcode';

    }
}