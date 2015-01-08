<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace Symbb\Extension\RatingBundle\EventListener;

class TemplateListener
{
    
    public function beforeActions(\Symbb\Core\EventBundle\Event\TemplatePostEvent $event)
    {
        $event->render('SymbbExtensionRatingBundle:Post:rating.html.twig', array("post" => $event->getPost()));
    }
    
    public function topicStylesheets(\Symbb\Core\EventBundle\Event\TemplateDefaultEvent $event){
        $event->render('SymbbExtensionRatingBundle::stylesheets.html.twig', array());
    }
}