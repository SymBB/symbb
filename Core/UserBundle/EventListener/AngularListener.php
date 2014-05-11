<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\UserBundle\EventListener;

class AngularListener
{

    public function routerFiles($event)
    {
        $event->addFile(__DIR__.'/../Resources/config/angular/config.yml');
    }
}