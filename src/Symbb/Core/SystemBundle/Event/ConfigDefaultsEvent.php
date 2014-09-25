<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SystemBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ConfigDefaultsEvent extends Event
{

    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection 
     */
    protected $configList;

    protected $section = 'default';

    public function __construct(\Doctrine\Common\Collections\ArrayCollection $configList)
    {
        $this->configList = $configList;
    }

    public function getConfigList()
    {
        return $this->configList;
    }

    public function setDefaultConfig($key, $value, $type, $section = "default")
    {
        $valueArray = new \Doctrine\Common\Collections\ArrayCollection();
        $valueArray->set('value', $value);
        $valueArray->set('section', $section);
        $valueArray->set('key', $key);
        $valueArray->set('type', $type);
        $this->configList->set($section.'.'.$key, $valueArray);
    }
}
