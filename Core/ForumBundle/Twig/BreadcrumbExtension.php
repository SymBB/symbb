<?
/**
*
* @package symBB
* @copyright (c) 2013 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\ForumBundle\Twig;

class BreadcrumbExtension extends \Twig_Extension
{
    protected $translator;
    protected $router;
    protected $env;
    protected $config = array();

    public function __construct($container) {
        $this->translator       = $container->get('translator');
        $this->router           = $container->get('router');
        $this->configManager    = $container->get('symbb.core.config.manager');
    }

    public function initRuntime(\Twig_Environment $environment){
        parent::initRuntime($environment);
        $this->env = $environment;
    }
    
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getSymbbBreadcrumb', array($this, 'getSymbbBreadcrumb'), array(
                'is_safe' => array('html')
            )),
        );
    }

    public function getSymbbBreadcrumb($object = null)
    {
        
        $breadcrumb = array();
        if($object instanceof \SymBB\Core\ForumBundle\Entity\Forum){
            $breadcrumb = $this->createForForum($object, $breadcrumb);
        } else if($object instanceof \SymBB\Core\ForumBundle\Entity\Topic){
            $breadcrumb = $this->createForTopic($object, $breadcrumb);
        }
        
        $home           = $this->translator->trans('Home', array(), 'symbb_frontend');
        $uri            = $this->router->generate('_symbb_index');
        $breadcrumb[]   = array('name' => $home, 'link' => $uri);
        $breadcrumb     = array_reverse($breadcrumb);
        
        $html = $this->env->render(
            $this->configManager->get('template.forum').'::breadcrumb.html.twig',
            array('breadcrumb' => $breadcrumb)
        );
        
        return $html;
    }
    
    protected function createForForum(\SymBB\Core\ForumBundle\Entity\Forum $object, $breadcrumb){
        while (is_object($object)) {
            if(is_object($object)){
                $uri = $this->router->generate('symbb_forum_show', array('id' => $object->getId(), 'name' => $object->getSeoName()));
                $breadcrumb[]   = array('name' => $object->getName(), 'link' => $uri);
            }
            $object = $object->getParent();
        };
        return $breadcrumb;
    }
    
    protected function createForTopic(\SymBB\Core\ForumBundle\Entity\Topic $object, $breadcrumb){
        if($object->getId() > 0){
            $uri            = $this->router->generate('symbb_forum_topic_show', array('id' => $object->getId(), 'name' => $object->getSeoName()));
            $breadcrumb[]   = array('name' => $object->getName(), 'link' => $uri);
        }
        $forum          = $object->getForum();
        $breadcrumb     = $this->createForForum($forum, $breadcrumb);
        return $breadcrumb;
    }

    public function getName()
    {
        return 'symbb_forum_breadcrumb';
    }
}