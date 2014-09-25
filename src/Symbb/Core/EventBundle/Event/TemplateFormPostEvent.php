<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\EventBundle\Event;

class TemplateFormPostEvent extends BaseTemplateEvent
{

    public function __construct($env)
    {
        $this->env = $env;
    }
}
