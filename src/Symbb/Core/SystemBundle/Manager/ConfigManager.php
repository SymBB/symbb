<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SystemBundle\Manager;

class ConfigManager
{

    /**
     * @var \Doctrine\ORM\EntityManager 
     */
    protected $em;

    /**
     * @var \Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher
     */
    protected $dispatcher;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $defaults;
    
    protected $container;

    /**
     * 
     * @param type $em
     */
    public function __construct($container)
    {
        $this->em = $container->get("doctrine.orm.symbb_entity_manager");
        $this->dispatcher = $container->get('event_dispatcher');
        $this->container = $container;
        $this->defaults = new \Doctrine\Common\Collections\ArrayCollection();
        $event = new \Symbb\Core\SystemBundle\Event\ConfigDefaultsEvent($this->defaults);
        $this->dispatcher->dispatch('symbb.config.configs', $event);
    }

    /**
     * 
     * @param type $key
     * @param type $section
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDefault($key, $section = "default")
    {
        $data = null;
        if ($this->defaults->containsKey($section . '.' . $key)) {
            $data = $this->defaults->get($section . '.' . $key);
        }
        return $data;
    }

    public function get($key, $section = 'default')
    {
        $value = null;
        $default = $this->getDefault($key, $section);

        if ($default) {

            $config = $this->em->getRepository('SymbbCoreSystemBundle:Config')->findOneBy(array('key' => $default->get('key'), 'section' => $default->get('section')));

            if ($config) {
                $value = $config->getValue();
            }

            if ($value === null) {
                $value = $default->get('value');
            }
        }

        return $value;
    }

    public function set($key, $section, $value, $type = null)
    {

        
        $config = $this->em->getRepository('SymbbCoreSystemBundle:Config')->findOneBy(array('key' => $key, 'section' => $section));

        if (!$config) {
            $config = new \Symbb\Core\SystemBundle\Entity\Config();
            $config->setKey($key);
            $config->setSection($section);
        }

        if ($type === null) {
            $type = $this->getDBField($key);
        }

        $config->setValue($value, $type);

        $this->em->persist($config);
    }

    public function save()
    {
        $this->em->flush();
    }

    /**
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    public function getChoices($key, $section = "default")
    {
        $event = new \Symbb\Core\SystemBundle\Event\ConfigChoicesEvent($key, $section, $this->container);
        $this->dispatcher->dispatch('symbb.config.choices', $event);
        $options = $event->getChoices();
        return $options;
    }

    public function getType($key, $section = "default")
    {
        $type = "string";

        $default = $this->getDefault($key, $section);

        if ($default) {
            $type = $default->get('type');
        }

        return $type;
    }

    public function getDBField($key, $section = "default")
    {

        $type = $this->getType($key, $section);

        if ($type === 'date') {
            $type = 'datetime';
        } else if ($type === 'textarea' || $type === 'bbcode') {
            $type = 'text';
        } else if ($type === 'int' || $type === 'number') {
            $type = 'integer';
        } else if ($type === 'boolean' || $type === 'checkbox') {
            $type = 'boolean';
        } else {
            $type = 'string';
        }

        return $type;
    }

    public function getSection($key)
    {
        $section = "default";
        $default = $this->getDefault($key, $section);

        if ($default) {
            $section = $default->get('section');
        }
        return $section;
    }

    public function getConfigListGroupBySection()
    {
        $configs = $this->getDefaults();
        $finalConfig = array();
        foreach ($configs as $valueArray) {
            $value = $valueArray->get('value');
            $section = $valueArray->get('section');
            $key2 = $valueArray->get('key');
            $finalConfig[$section][$key2] = $value;
        }
        return $finalConfig;
    }

    public function insertDefault()
    {

        $configs = $this->getDefaults();

        foreach ($configs as $valueArray) {
            $value = $valueArray->get('value');
            $section = $valueArray->get('section');
            $key2 = $valueArray->get('key');
            $config = new \Symbb\Core\SystemBundle\Entity\Config();
            $config->setKey($key2);
            $config->setValue($value);
            $config->setSection($section);
            $this->em->persist($config);
        }

        $this->em->flush();
    }

    public function getSymbbConfig($key){
        $config = $this->container->getParameter('symbb_config');
        return $config[$key];
    }
}
