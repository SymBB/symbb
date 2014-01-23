<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\ForumBundle\EventListener;

class ConfigListener
{
    
    public function defaults(\SymBB\Core\SystemBundle\Event\ConfigDefaultsEvent $event)
    {
        $event->setDefaultConfig('forum.newpost.max', '10');
    }
    
    public function type(\SymBB\Core\SystemBundle\Event\ConfigTypeEvent $event)
    {
        $key = $event->getKey();
        if(
            $key == 'forum.newpost.max'
        ){
            $event->setType('number');
        }
    }
    
    public function section(\SymBB\Core\SystemBundle\Event\ConfigSectionEvent $event)
    {
        $key = $event->getKey();
        if(
            $key == 'forum.newpost.max'
        ){
            $event->setSection('forum');
        }
    }
}