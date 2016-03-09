<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\MessageBundle\Form;

use Symbb\Core\MessageBundle\Form\DataTransformer\UsersToReceiverTransformer;
use Symbb\Core\UserBundle\Manager\UserManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class Message extends AbstractType
{

    protected $full = true;
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var Message
     */
    protected $message;

    public function __construct($full, UserManager $userManager, \Symbb\Core\MessageBundle\Entity\Message $message ){
        $this->full = $full;
        $this->userManager = $userManager;
        $this->message = $message;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Symbb\Core\MessageBundle\Entity\Message',
            'translation_domain' => 'symbb_frontend',
            'cascade_validation' => true,
            'error_bubbling' => true
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        if($this->full){

            $transformer = new UsersToReceiverTransformer($this->message, $this->userManager);

            $users = $this->userManager->findUsers(999999, 1);

            $receivers = $builder->create('receivers', 'entity', array(
                'choices' => $users,
                'class' => 'SymbbCoreUserBundle:User',
                'required' => true,
                "multiple" => true
            ))->addModelTransformer($transformer);

            $builder->add($receivers);
        }

        $builder
            ->add('subject', 'text', array("required" => true))
            ->add('message', 'textarea', array("required" => true));
    }

    public function getName()
    {
        return 'message';
    }
}