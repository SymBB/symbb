<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\UserBundle\Form\Type;

use Symbb\Core\UserBundle\Entity\User;
use Symbb\Core\UserBundle\Manager\UserManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class Option extends AbstractType
{

    protected $fields;

    protected $entity;

    /**
     * @var UserManager
     */
    protected $usermanager;

    public function __construct($fields, $entity, UserManager $usermanager)
    {
        $this->fields = $fields;
        $this->entity = $entity;
        $this->usermanager = $usermanager;
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
            ->add('signature', 'textarea', array('required' => false, 'attr' => array('placeholder' => 'Give Your text here')))
            ->add('timezone', 'timezone', array('required' => true));

        $currValues = $this->entity->getFieldValues();

        $fields = $this->fields;
        foreach ($fields as $field) {
            $data = null;
            foreach ($currValues as $currValue) {
                if ($currValue->getField()->getId() === $field->getId()) {
                    $data = $currValue->getValue();
                    break;
                }
            }
            $builder->add('field_' . $field->getId(), $field->getFormType(), array('required' => false, 'mapped' => false, 'data' => $data, 'label' => $field->getLabel()));
        }
    }

    public function getName()
    {
        return 'user_options';
    }
}