<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\BBCodeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class AngularController extends \Symbb\Core\SystemBundle\Controller\AbstractController
{

    public function templateFileAction($file, $set = 1)
    {
        $response = $this->render(
            'SymbbCoreBBCodeBundle:Angular:' . $file . '.html.twig',
            array('bbcodeset' => $set)
        );

        $response->setPublic();
        $response->setSharedMaxAge(600);

        return $response;
    }

}