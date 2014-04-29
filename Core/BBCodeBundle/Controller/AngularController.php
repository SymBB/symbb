<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\BBCodeBundle\Controller;

class AngularController extends \SymBB\Core\SystemBundle\Controller\AbstractController
{

    public function templateFileAction($file, $set = 1)
    {
        $response = $this->render(
            'SymBBCoreBBCodeBundle:Angular:'.$file.'.html.twig',
            array('bbcodeset' => $set)
        );
        
        $response->setPublic();
        $response->setSharedMaxAge(600);
        
        return $response;
    }
    
}