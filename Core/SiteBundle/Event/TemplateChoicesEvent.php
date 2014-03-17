<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SiteBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class TemplateChoicesEvent extends Event
{

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection 
     */
    protected $options;

    public function __construct()
    {
        $this->options = new \Doctrine\Common\Collections\ArrayCollection;
    }

    public function addChoice($choiceKey, $choiceText)
    {
        $this->options->set($choiceKey, $choiceText);
    }

    public function getChoices()
    {
        return $this->options;
    }
}
