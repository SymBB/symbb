<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Symbb\Core\EventBundle\Twig;

use \Symbb\Core\EventBundle\Event\TemplatePostEvent;
use \Symbb\Core\EventBundle\Event\TemplateFormPostEvent;
use \Symbb\Core\EventBundle\Event\TemplateFormTopicEvent;

class EventExtension extends \Twig_Extension
{
    protected $dispatcher;
    protected $env;

    public function __construct($dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('executeSymbbTemplateFormTopicEvent', array($this, 'executeSymbbTemplateFormTopicEvent'), array(
                'is_safe' => array('html'),
                'needs_environment' => true
            )),
            new \Twig_SimpleFunction('executeSymbbTemplateFormPostEvent', array($this, 'executeSymbbTemplateFormPostEvent'), array(
                'is_safe' => array('html'),
                'needs_environment' => true
            )),
            new \Twig_SimpleFunction('executeSymbbTemplatePostEvent', array($this, 'executeSymbbTemplatePostEvent'), array(
                'is_safe' => array('html'),
                'needs_environment' => true
            )),
            new \Twig_SimpleFunction('executeSymbbEvent', array($this, 'executeSymbbEvent'), array(
                'is_safe' => array('html'),
                'needs_environment' => true
            )),
        );
    }

    public function executeSymbbEvent($env, $eventName)
    {
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->dispatcher;
        $event = new \Symbb\Core\EventBundle\Event\TemplateDefaultEvent($env);
        $dispatcher->dispatch('symbb.' . $eventName, $event);
        $html = $event->getHtml();
        return $html;
    }


    public function executeSymbbTemplateFormTopicEvent($env, $eventName, $form)
    {
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->dispatcher;
        $event = new TemplateFormTopicEvent($env, $form);
        $dispatcher->dispatch('symbb.core.forum.topic.template.form.' . $eventName, $event);
        $html = $event->getHtml();
        return $html;
    }

    public function executeSymbbTemplateFormPostEvent($env, $eventName, $form)
    {
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->dispatcher;
        $event = new TemplateFormPostEvent($env, $form);
        $dispatcher->dispatch('symbb.core.forum.post.template.form.' . $eventName, $event);
        $html = $event->getHtml();
        return $html;
    }

    public function executeSymbbTemplatePostEvent($env, $eventName, $post)
    {
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->dispatcher;
        $event = new TemplatePostEvent($env, $post);
        $dispatcher->dispatch('symbb.core.forum.post.template.' . $eventName, $event);
        $html = $event->getHtml();
        return $html;
    }

    public function getName()
    {
        return 'symbb_core_event';
    }
}