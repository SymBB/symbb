<?php
/**
*
* @package symBB
* @copyright (c) 2013 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\SystemBundle\EventListener;

class ConfigListener
{
    
    public function defaults(\SymBB\Core\SystemBundle\Event\ConfigDefaultsEvent $event)
    {
        $event->setDefaultConfig('system.name', 'SymBB Test System');
        $event->setDefaultConfig('system.email', 'your@email.com');
    }
    
    public function type(\SymBB\Core\SystemBundle\Event\ConfigTypeEvent $event)
    {
        $key = $event->getKey();
        
        if(
            $key == 'system.name'
        ){
            $event->setType('text');
        } else if(
            $key == 'system.email'
        ){
            $event->setType('email');
        }
    }
}