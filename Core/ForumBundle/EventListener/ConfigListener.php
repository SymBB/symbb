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

    public function configs(\SymBB\Core\SystemBundle\Event\ConfigDefaultsEvent $event)
    {
        $event->setDefaultConfig('newpost.max', '10', 'number', $this->getSection());
    }

    protected function getSection()
    {
        return "forum";
    }
}
