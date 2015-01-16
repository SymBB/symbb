<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\SurveyBundle\EventListener;

use Symbb\Core\EventBundle\Event\FormPostEvent;
use Symbb\Core\ForumBundle\Entity\Post;
use Symbb\Core\UserBundle\Manager\UserManager;
use Symbb\Extension\SurveyBundle\Entity\Survey;
use Symbb\Extension\SurveyBundle\Form\SurveyType;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class FormListener
{
    /**
     * @var \Symbb\Core\UserBundle\Entity\UserInterface
     */
    protected $user;

    protected $em;

    public function __construct(UserManager $usermanager, $em)
    {
        $this->user = $usermanager->getCurrentUser();
        $this->em = $em;
    }

    public function postForm(FormPostEvent $event)
    {
        $builder = $event->getBuilder();
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder) {
            $form = $event->getForm();
            $data = $event->getData();

            /* Check we're looking at the right data/form */
            if ($data instanceof Post) {
                $repo = $this->em->getRepository('SymbbExtensionSurveyBundle:Survey');
                $survey = $repo->findOneBy(array('post' => $data->getId()));

                if (!is_object($survey)) {
                    $survey = new Survey();
                    $survey->setPost($data);
                    $survey->setEnd(null);
                }
                if (!$survey->getEnd()) {
                    $survey->setEnd(null);
                }

                $form->add("extensionSurvey", new SurveyType($this->user), array("data" => $survey));
            }
        });
    }
}