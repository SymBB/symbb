<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Template\DefaultBundle\EventListener;

class SiteListener
{
    public function templateChoices(\SymBB\Core\SiteBundle\Event\TemplateChoicesEvent $event)
    {
        $event->addChoice('SymBBTemplateDefaultBundle', '[Symbb] Default Template');
    }
}