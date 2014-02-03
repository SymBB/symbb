<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Extension\CalendarBundle\EventListener;

class FormListener
{

    public function addPostFormPart(\SymBB\Core\EventBundle\Event\FormPostEvent $event)
    {
        $builder = $event->getBuilder();
        $groupManager = $event->getGroupManager();
        $groups = $groupManager->findGroups();
        $groupArray = array();

        foreach ($groups as $group) {
            $groupArray[$group->getId()] = $group->getName();
        }
        
        $tz = $event->getUserManager()->getTimezone();
        
        $now = new \DateTime();
        $now->setTimezone($tz);
        
        $builder->add('calendarStartDate', 'datetime', array(
            'mapped' => false,
            'required' => false,
            'label' => 'Start Date',
            'input' => 'datetime',
            'widget' => 'single_text',
            'view_timezone' => $tz->getName(),
            'format' => \IntlDateFormatter::MEDIUM,
            'attr' => array(
                'class' => 'datetime'
            )
        ));

        $builder->add('calendarEndDate', 'datetime', array(
            'mapped' => false,
            'required' => false,
            'label' => 'End Date',
            'input' => 'datetime',
            'widget' => 'single_text',
            'view_timezone' => $tz->getName(),
            'format' => \IntlDateFormatter::MEDIUM,
            'attr' => array(
                'class' => 'datetime'
            )
        ));

        $builder->add('calendarGroups', 'choice', array(
            'mapped' => false,
            'required' => false,
            'label' => 'Visible to',
            'choices' => $groupArray,
            'multiple' => true
        ));

    }
}