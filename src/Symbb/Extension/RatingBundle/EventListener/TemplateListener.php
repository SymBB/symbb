<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Extension\RatingBundle\EventListener;

class TemplateListener
{
    
    public function beforeActions(\SymBB\Core\EventBundle\Event\TemplatePostEvent $event)
    {
        $event->render('SymBBExtensionRatingBundle:Post:rating.html.twig', array());
    }
    
    public function topicStylesheets(\SymBB\Core\EventBundle\Event\TemplateDefaultEvent $event){
        $event->render('SymBBExtensionRatingBundle::stylesheets.html.twig', array());
    }
}