<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Symbb\Core\ForumBundle\Twig;

use Symbb\Core\ForumBundle\DependencyInjection\TopicFlagHandler;
use Symbb\Core\SystemBundle\Manager\AbstractFlagHandler;

class TopicDataExtension extends \Twig_Extension
{

    protected $paginator;
    protected $em;
    /**
     * @var TopicFlagHandler
     */
    protected $topicFlagHandler;
    protected $siteManager;
    protected $securityContext;
    protected $translator;
    protected $request;
    protected $dispatcher;

    public function __construct($container)
    {
        $this->paginator = $container->get('knp_paginator');
        $this->em = $container->get('doctrine.orm.symbb_entity_manager');
        $this->topicFlagHandler = $container->get('symbb.core.topic.flag');
        $this->siteManager = $container->get('symbb.core.site.manager');
        $this->securityContext = $container->get('security.token_storage');
        $this->translator = $container->get('translator');
        $this->dispatcher = $container->get('event_dispatcher');

    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('checkSymbbForTopicNewFlag', array($this, 'checkSymbbForNewPostFlag')),
            new \Twig_SimpleFunction('checkSymbbForTopicAnsweredFlag', array($this, 'checkSymbbForAnsweredPostFlag')),
            new \Twig_SimpleFunction('checkSymbbForTopicFlag', array($this, 'checkForFlag')),
            new \Twig_SimpleFunction('getSymbbTopicLabels', array($this, 'getLabels')),
        );
    }

    public function checkSymbbForNewPostFlag($element)
    {
        return $this->checkForFlag($element, AbstractFlagHandler::FLAG_NEW);
    }

    public function checkSymbbForAnsweredPostFlag($element)
    {
        return $this->checkForFlag($element, AbstractFlagHandler::FLAG_NEW);
    }

    public function checkForFlag($element, $flag)
    {
        $check = $this->topicFlagHandler->checkFlag($element, $flag);
        return $check;
    }

    protected function getTemplateBundleName($for = 'forum')
    {
        return $this->siteManager->getTemplate($for);
    }

    public function getLabels(\Symbb\Core\ForumBundle\Entity\Topic $element)
    {
        $labels = array();

        $flags = $this->topicFlagHandler->findAll($element);

        foreach ($flags as $flag) {

            $label = array(
                'title' => $flag->getFlag(),
                'type' => 'default'
            );

            if ($label["title"] == "new") {
                $label["type"] = 'success';
            } else if ($label["title"] == "answered") {
                $label["type"] = 'info';
            } else if ($label["title"] == "locked") {
                $label["type"] = 'warning';
            }

            $labels[$label["title"]] = $label;
        }

        $event = new \Symbb\Core\EventBundle\Event\TopicLabelsEvent($element, $labels);
        $this->dispatcher->dispatch('symbb.topic.labels', $event);

        $labels = $event->getLabels();

        foreach ($labels as $key => $label) {
            $labels[$key]['title'] = $this->translator->trans($label['title'], array(), 'symbb_frontend');
        }

        return $labels;
    }


    public function getName()
    {
        return 'symbb_forum_topic_data';
    }

}