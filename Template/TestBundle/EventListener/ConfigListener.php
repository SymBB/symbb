<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Template\TestBundle\EventListener;

class ConfigListener
{


    public function choices(\SymBB\Core\SystemBundle\Event\ConfigChoicesEvent $event)
    {
        $key = $event->getKey();
        if (
            $key == 'template.acp' ||
            $key == 'template.forum' ||
            $key == 'template.portal' ||
            $key == 'template.email'
        ) {
            $event->addChoice('SymBBTemplateTestBundle', 'Test Template');
        }
    }
}