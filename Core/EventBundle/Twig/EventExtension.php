<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\EventBundle\Twig;

use \SymBB\Core\EventBundle\Event\TemplatePostEvent;
use \SymBB\Core\EventBundle\Event\TemplateTopicEvent;
use \SymBB\Core\EventBundle\Event\TemplateFormPostEvent;
use \SymBB\Core\EventBundle\Event\TemplateFormTopicEvent;

class EventExtension extends \Twig_Extension
{
    protected $container;
    protected $env;

    public function __construct($container) {
        $this->container        = $container;
    }

    public function initRuntime(\Twig_Environment $environment){
        parent::initRuntime($environment);
        $this->env = $environment;
    }
    
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('executeSymbbTemplatePostEvent', array($this, 'executeSymbbTemplatePostEvent'), array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('executeSymbbTemplateTopicEvent', array($this, 'executeSymbbTemplateTopicEvent'), array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('executeSymbbTemplateFormTopicEvent', array($this, 'executeSymbbTemplateFormTopicEvent'), array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('executeSymbbTemplateFormPostEvent', array($this, 'executeSymbbTemplateFormPostEvent'), array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('executeSymbbEvent', array($this, 'executeSymbbEvent'), array(
                'is_safe' => array('html')
            )),
        );
    }

    public function executeSymbbEvent($eventName)
    {
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');
        $event      = new \SymBB\Core\EventBundle\Event\TemplateDefaultEvent($this->env);
        $dispatcher->dispatch('symbb.'.$eventName, $event);
        $html       = $event->getHtml();
        return $html;
    }

    public function executeSymbbTemplateFormTopicEvent($eventName, $form)
    {
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');
        $event      = new TemplateFormTopicEvent($this->env, $form);
        $dispatcher->dispatch('symbb.topic.template.form.'.$eventName, $event);
        $html       = $event->getHtml();
        return $html;
    }
    
    public function executeSymbbTemplateFormPostEvent($eventName, $form)
    {
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');
        $event      = new TemplateFormPostEvent($this->env, $form);
        $dispatcher->dispatch('symbb.post.template.form.'.$eventName, $event);
        $html       = $event->getHtml();
        return $html;
    }

    public function executeSymbbTemplatePostEvent($eventName)
    {
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');
        $event      = new TemplatePostEvent($this->env);
        $dispatcher->dispatch('symbb.post.template.'.$eventName, $event);
        $html       = $event->getHtml();
        return $html;
    }

    public function executeSymbbTemplateTopicEvent($eventName, \SymBB\Core\ForumBundle\Entity\Topic $topic)
    {
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');
        $event      = new TemplateTopicEvent($this->env, $topic);
        $dispatcher->dispatch('symbb.topic.template.'.$eventName, $event);
        $html       = $event->getHtml();
        return $html;
    }
    
    public function getName()
    {
        return 'symbb_core_event';
    }
}