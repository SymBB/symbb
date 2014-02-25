<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SystemBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ConfigChoicesEvent extends Event
{

    protected $key;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection 
     */
    protected $options;

    protected $section = "default";

    public function __construct($key, $section)
    {
        $this->key = $key;
        $this->section = $section;
        $this->options = new \Doctrine\Common\Collections\ArrayCollection;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function addChoice($choiceKey, $choiceText)
    {
        $this->options->set($choiceKey, $choiceText);
    }

    public function getChoices()
    {
        return $this->options;
    }

    public function setSection($section)
    {
        $this->section = $section;
    }

    public function getSection()
    {
        return $this->section;
    }
}
