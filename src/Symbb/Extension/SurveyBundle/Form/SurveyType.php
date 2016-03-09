<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\SurveyBundle\Form;

use Symbb\Core\UserBundle\Entity\UserInterface;
use Symbb\Core\UserBundle\Manager\UserManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SurveyType extends AbstractType
{

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var UserManager
     */
    protected $usermanager;

    public function __construct(UserInterface $user, UserManager $usermanager)
    {
        $this->user = $user;
        $this->usermanager = $usermanager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $timezone = $this->usermanager->getSymbbData($this->user)->getTimezone();
        $builder
            ->add('question', 'text', array("required" => false))
            ->add('answers', "text", array("required" => false))
            ->add('choices', "number", array("required" => false))
            ->add('choicesChangeable', "checkbox", array("required" => false))
            ->add('end', "datetime", array("required" => false, "input" => "timestamp", "view_timezone" => $timezone, "widget" => "single_text"));
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