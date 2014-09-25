<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\DependencyInjection;

use \Symbb\Core\ForumBundle\Entity\Topic;
use \Doctrine\ORM\EntityManager;
use \Symbb\Core\SystemBundle\Manager\ConfigManager;

class NotifyHandler extends \Symbb\Core\SystemBundle\Manager\AbstractManager
{

    /**
     *
     * @var EntityManager
     */
    protected $em;

    /**
     *
     * @var TopicFlagHandler
     */
    protected $flagHandler;

    /**
     *
     * @var ConfigManager
     */
    protected $configManager;

    protected $mailer;

    protected $container;

    public function __construct($container)
    {
        $this->em = $container->get('doctrine.orm.symbb_entity_manager');
        $this->securityContext = $container->get('security.context');
        $this->flagHandler = $container->get('symbb.core.topic.flag');
        $this->mailer = $container->get('swiftmailer.mailer.default');
        $this->translator = $container->get('translator');
        $this->container = $container;
        $this->configManager = $container->get('symbb.core.config.manager');
        

    }
    
    public function getLocale(){
        $locale = $this->container->get('request')->getLocale();
        if (strpos('_', $locale) !== false) {
            $locale = explode('_', $locale);
            $locale = reset($locale);
        }
        return $locale;
    }

    public function sendTopicNotifications(Topic $topic, $user)
    {

        if (is_numeric($user)) {
            $user = $this->em->getRepository('SymbbCoreUserBundle:User')->find($user);
        }

        $subject = $this->translator->trans('It was written a new answer to "%topic%"', array('%topic%' => $topic->getName()), 'symbb_email');
        $sender = $this->configManager->get('system.email');
        $recipient = $user->getEmail();

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($sender)
            ->setTo($recipient)
            ->setBody(
            $this->container->get('twig')->render(
                $this->configManager->get('template.email') . ':Email:topic_notify.' . $this->getLocale() . '.html.twig', array('topic' => $topic, 'user' => $user, 'symbbConfigManager' => $this->configManager)
            ), 'text/html'
            )
        ;

        $this->mailer->send($message);

    }
}
