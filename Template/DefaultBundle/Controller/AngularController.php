<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Template\DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AngularController extends Controller
{

    public function templateFileAction($file)
    {
        return $this->render(
            'SymBBTemplateDefaultBundle:Angular:'.$file.'.html.twig',
            array()
        );
    }
}