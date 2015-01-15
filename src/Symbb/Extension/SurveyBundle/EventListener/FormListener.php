<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\SurveyBundle\EventListener;

use Symbb\Core\EventBundle\Event\FormPostEvent;
use Symbb\Extension\SurveyBundle\Form\SurveyType;
use Symfony\Component\Security\Core\SecurityContextInterface;


class FormListener
{
    public function postForm(FormPostEvent $event){
        $event->getBuilder()->add("extensionSurvey", new SurveyType());
    }
}