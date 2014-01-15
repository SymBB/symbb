<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Template\SimpleBundle\EventListener;

class ConfigListener
{
    
    public function defaults(\SymBB\Core\SystemBundle\Event\ConfigDefaultsEvent $event)
    {
        $event->setDefaultConfig('template.acp', 'SymBBTemplateSimpleBundle');
        $event->setDefaultConfig('template.forum', 'SymBBTemplateSimpleBundle');
        $event->setDefaultConfig('template.portal', 'SymBBTemplateSimpleBundle');
        $event->setDefaultConfig('template.email', 'SymBBTemplateSimpleBundle');
    }
    
    public function choices(\SymBB\Core\SystemBundle\Event\ConfigChoicesEvent $event)
    {
        $key = $event->getKey();
        if(
            $key == 'template.acp' ||
            $key == 'template.forum' ||
            $key == 'template.portal' ||
            $key == 'template.email'
        ){
            $event->addChoice('SymBBTemplateSimpleBundle', 'Default Template');
        }
    }
    
    public function type(\SymBB\Core\SystemBundle\Event\ConfigTypeEvent $event)
    {
        $key = $event->getKey();
        if(
            $key == 'template.acp' ||
            $key == 'template.forum' ||
            $key == 'template.portal' ||
            $key == 'template.email'
        ){
            $event->setType('choice');
        }
    }
    
    public function section(\SymBB\Core\SystemBundle\Event\ConfigSectionEvent $event)
    {
        $key = $event->getKey();
        if(
            $key == 'template.acp' ||
            $key == 'template.forum' ||
            $key == 'template.portal' ||
            $key == 'template.email'
        ){
            $event->setSection('template');
        }
    }
}