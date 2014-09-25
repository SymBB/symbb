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

class Option extends AbstractType
{

    protected $em;

    protected $entity;

    public function __construct($em, $entity)
    {
        $this->em = $em;
        $this->entity = $entity;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'symbb_frontend'
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('avatar', 'text', array('required' => false, 'attr' => array('placeholder' => 'http://deine-avatar.url')))
            ->add('signature', 'textarea', array('required' => false, 'attr' => array()))
            ->add('timezone', 'timezone', array('required' => true));

        $currValues = $this->entity->getFieldValues();

        $fields = $this->em->getRepository('SymbbCoreUserBundle:Field')->findBy(array(), array('position' => 'asc', 'id' => 'asc'));
        foreach ($fields as $field) {
            $data = null;
            foreach ($currValues as $currValue) {
                if ($currValue->getField()->getId() === $field->getId()) {
                    $data = $currValue->getValue();
                    break;
                }
            }
            $builder->add('field:' . $field->getId(), $field->getFormType(), array('required' => false, 'mapped' => false, 'data' => $data, 'label' => $field->getLabel()));
        }
    }

    public function getName()
    {
        return 'user_options';
    }
}