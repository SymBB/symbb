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

class DisplayController extends Controller
{

    public function defaultAction()
    {
        return $this->render(
            'SymBBExtensionCalendarBundle::default.html.twig', array()
        );

    }
}