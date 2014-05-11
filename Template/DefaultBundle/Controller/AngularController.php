<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Template\DefaultBundle\Controller;

class AngularController extends \SymBB\Core\SystemBundle\Controller\AbstractController
{

    public function templateFileAction($file)
    {
        $response = $this->render(
            $this->getTemplateBundleName('forum').':Angular:'.$file.'.html.twig',
            array()
        );
        
        // all angular templates should be public, they dont contains some private informations. 
        // all variables are parsed at the frontend
        $response->setPublic();
        $response->setSharedMaxAge(600);
        
        return $response;
    }
    
}