<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\SurveyBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SurveyType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('question', 'text', array("required" => false))
            ->add('answers', "text", array("required" => false))
            ->add('choices', "number", array("required" => false))
            ->add('choicesChangeable', "choice", array("required" => false))
            ->add('end', "datetime", array("required" => false));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Symbb\Extension\SurveyBundle\Entity\Survey',
            'translation_domain' => 'symbb_frontend',
            'cascade_validation' => true,
            'mapped' => false,
            'label' => false
        ));
    }

    public function getName()
    {
        return 'survey';

    }
}