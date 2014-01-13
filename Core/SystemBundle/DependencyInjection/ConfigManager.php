<?php
/**
*
* @package symBB
* @copyright (c) 2013 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\SystemBundle\DependencyInjection;


class ConfigManager {
    
    /**
     * @var \Doctrine\ORM\EntityManager 
     */
    protected $em;
    
    /**
     * @var \Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher
     */
    protected $dispatcher;
    
    /**
     * 
     * @param type $em
     */
    public function __construct($em, $dispatcher) {
        $this->em  = $em;
        $this->dispatcher = $dispatcher;
    }
    
    public function get($key){
        $config = $this->em->getRepository('SymBBCoreSystemBundle:Config')->findOneBy(array('key' => $key));
        $value = null;
        if($config){
            $value = $config->getValue();
        }
        if($value === null){
            $defaults = $this->getDefaults();
            if($defaults->containsKey($key)){
                $value = $defaults->get($key);
            }
        }
        return $value;
    }
    
    public function set($key, $value){
        $config = $this->em->getRepository('SymBBCoreSystemBundle:Config')->findOneBy(array('key' => $key));
        if(!$config){
            $config = new \SymBB\Core\SystemBundle\Entity\Config();
            $config->setKey($key);
        }
        $config->setValue($value);
        $this->em->persist($config);
    }
    
    public function save(){
        $this->em->flush();
    }
    
    /**
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDefaults(){
        
        $collection = new \Doctrine\Common\Collections\ArrayCollection();
        
        $event = new \SymBB\Core\SystemBundle\Event\ConfigDefaultsEvent($collection);
        
        $this->dispatcher->dispatch('symbb.config.defaults', $event);
        
        return $collection;
    }
    
    public function getChoices($key){
        $event      = new \SymBB\Core\SystemBundle\Event\ConfigChoicesEvent($key);
        $this->dispatcher->dispatch('symbb.config.choices', $event);
        $options    = $event->getChoices();
        return $options;
    }
    
    public function getType($key){
        $event      = new \SymBB\Core\SystemBundle\Event\ConfigTypeEvent($key);
        $this->dispatcher->dispatch('symbb.config.type', $event);
        $type       = $event->getType();
        return $type;
    }
    
    public function getSection($key){
        $event      = new \SymBB\Core\SystemBundle\Event\ConfigSectionEvent($key);
        $this->dispatcher->dispatch('symbb.config.section', $event);
        $section    = $event->getSection();
        return $section;
    }
    
    public function getConfigListGroupBySection(){
        $configs = $this->getDefaults();
        $finalConfig = array();
        foreach($configs as $key => $value){
            $section = $this->getSection($key);
            $finalConfig[$section][$key] = $value;
        }
        return $finalConfig;
    }
    
    public function insertDefault(){
        
        $configs = $this->getDefaults();
        
        foreach($configs as $key => $value){
            $config = new \SymBB\Core\SystemBundle\Entity\Config();
            $config->setKey($key);
            $config->setValue($value);
            $this->em->persist($config);
        }
        
        $this->em->flush();
    }
}
