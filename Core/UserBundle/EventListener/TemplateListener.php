<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\UserBundle\EventListener;

use SymBB\Core\EventBundle\Event\BaseTemplateEvent;

class TemplateListener
{

    public function javascripts(BaseTemplateEvent $event)
    {
        $event->render('SymBBCoreUserBundle::javascripts.html.twig', array());
    }
}