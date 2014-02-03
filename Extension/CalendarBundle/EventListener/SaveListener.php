<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Extension\CalendarBundle\EventListener;

use \SymBB\Core\EventBundle\Event\EditPostEvent;
use \SymBB\Core\UserBundle\DependencyInjection\GroupManager;

class SaveListener
{

    protected $em;

    /**
     *
     * @var \SymBB\Core\UserBundle\DependencyInjection\GroupManager 
     */
    protected $groupManager;

    public function __construct($em, GroupManager $groupManager)
    {
        $this->em = $em;
        $this->groupManager = $groupManager;

    }

    public function save(EditPostEvent $event)
    {

        $post = $event->getPost();
        $form = $event->getForm();
        $start = $form->get('calendarStartDate')->getData();
        $end = $form->get('calendarEndDate')->getData();
        $groups = $form->get('calendarGroups')->getData();
        $name = $form->get('calendarName')->getData();

        if (!empty($start) && !empty($end)) {

            $repo = $this->em->getRepository('SymBBExtensionCalendarBundle:Event');
            $event = $repo->findOneBy(array('post' => $post->getId()));

            if (!$event) {
                $event = new \SymBB\Extension\CalendarBundle\Entity\Event();
                $event->setPost($post);
                if (empty($name)) {
                    $name = $post->getName();
                }
            }

            $event->setStartDate($start);
            $event->setEndDate($end);
            $event->setName($name);

            $finalGroups = array();

            foreach ($groups as $groupId) {
                $finalGroups[] = $this->groupManager->find($groupId);
            }

            $event->setGroups($finalGroups);

            $this->em->persist($event);
        }
        //flush will be execute in the controller

    }

    public function handleRequest(EditPostEvent $event)
    {

        $post = $event->getPost();
        $form = $event->getForm();

        if ($post->getId() > 0) {

            $repo = $this->em->getRepository('SymBBExtensionCalendarBundle:Event');
            $event = $repo->findOneBy(array('post' => $post));

            if (is_object($event) && !$form->isSubmitted()) {

                $groups = array();
                foreach ($event->getGroups() as $group) {
                    $groups[] = $group->getId();
                }

                $form->get('calendarStartDate')->setData($event->getStartDate());
                $form->get('calendarEndDate')->setData($event->getEndDate());
                $form->get('calendarGroups')->setData($groups);
                $form->get('calendarName')->setData($event->getName());
            }
        }

    }
}