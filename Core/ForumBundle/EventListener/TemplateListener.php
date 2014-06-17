<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\ForumBundle\EventListener;

class TemplateListener
{
    public function javascripts($event)
    {
        $event->render('SymBBCoreForumBundle::javascripts.html.twig', array());
    }
}