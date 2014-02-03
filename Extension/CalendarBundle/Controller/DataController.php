<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Extension\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DataController extends Controller
{

    public function eventsAction()
    {

        $events[] = array(
            'id' => 1,
            'title' => 'test',
            'url' => 'abc/url/1',
            'start' => time()
        );

        $data = array('success' => 1, 'result' => $events);

        $response = new \Symfony\Component\HttpFoundation\JsonResponse();
        $response->setData($data);

        return $response;

    }

    public function templateAction($template)
    {

        return $this->render(
            'SymBBExtensionCalendarBundle:Template:' . $template . '.html.twig', array()
        );

    }
}