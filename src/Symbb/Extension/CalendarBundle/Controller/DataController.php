<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DataController extends Controller
{

    public function eventsAction()
    {

        $from = $this->get('request')->get('from');
        $until = $this->get('request')->get('to');
        $eventList = array();

        if (\is_numeric($from) && $from > 0 && \is_numeric($until) && $until > 0) {


            $from = $from / 1000;
            $from = \DateTime::createFromFormat('U', $from);

            $until = $until / 1000;
            $until = \DateTime::createFromFormat('U', $until);

            if ($from && $until) {
                $em = $this->get('doctrine.orm.symbb_entity_manager');

                $query = $em->createQuery(
                        'SELECT 
                        e
                    FROM 
                        SymbbExtensionCalendarBundle:Event e
                    WHERE 
                        e.startDate >= :from AND 
                        e.startDate <= :until AND
                        e.post > 0
                    ORDER BY 
                        e.startDate, e.name ASC'
                    )
                    ->setParameter('from', $from->format('Y-m-d H:i:s'))
                    ->setParameter('until', $until->format('Y-m-d H:i:s'));

                $events = $query->getResult();

                $user = $this->get('symbb.core.user.manager');
                $userGroups = $user->getCurrentUser()->getGroups();

                foreach ($events as $event) {

                    $post = $event->getPost();
                    $groups = $event->getGroups();
                    $check = false;
                    foreach ($groups as $group) {
                        foreach ($userGroups as $userGroup) {
                            if ($userGroup->getId() == $group->getId()) {
                                $check = true;
                                break;
                            }
                        }
                        if ($check) {
                            break;
                        }
                    }

                    if ($post && $check) {

                        $topic = $post->getTopic();

                        $url = $this->generateUrl(
                            'symbb_forum_topic_show', array('name' => $topic->getSeoName(), 'id' => $topic->getId())
                        );

                        $url = $url . '#' . $post->getSeoName() . '-' . $post->getId();

                        $start = $event->getStartDate()->getTimestamp();
                        $start = $start * 1000;

                        $end = $event->getEndDate()->getTimestamp();
                        $end = $end * 1000;

                        $eventList[] = array(
                            'id' => $event->getId(),
                            'title' => $event->getName(),
                            'url' => $url,
                            'start' => $start,
                            'end' => $end
                        );
                    }
                }
            }
        }





        $data = array('success' => 1, 'result' => $eventList);

        $response = new \Symfony\Component\HttpFoundation\JsonResponse();
        $response->setData($data);

        return $response;

    }

    public function templateAction($template)
    {

        return $this->render(
                'SymbbExtensionCalendarBundle:Template:' . $template . '.html.twig', array()
        );

    }
}