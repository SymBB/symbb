<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Template\DefaultBundle\Controller;

class AngularController extends \Symbb\Core\SystemBundle\Controller\AbstractController
{

    public function acpIndexAction()
    {
        return $this->render($this->getTemplateBundleName('acp') . ':AcpAngular:index.html.twig', array());
    }

    public function acpTemplateFileAction($file)
    {

        $file = str_replace('|', '/', $file);
        $response = $this->render(
            $this->getTemplateBundleName('forum') . ':AcpAngular:' . $file . '.html.twig',
            array()
        );

        // all angular templates should be public, they dont contains some private informations.
        // all variables are parsed at the frontend
        $response->setPublic();
        $response->setSharedMaxAge(600);

        return $response;
    }

    public function forumTemplateFileAction($file)
    {
        $response = $this->render(
            $this->getTemplateBundleName('forum') . ':Angular:Forum/' . $file . '.html.twig',
            array()
        );

        // all angular templates should be public, they dont contains some private informations.
        // all variables are parsed at the frontend
        $response->setPublic();
        $response->setSharedMaxAge(600);

        return $response;
    }

    public function templateFileAction($file)
    {
        $response = $this->render(
            $this->getTemplateBundleName('forum') . ':Angular:' . $file . '.html.twig',
            array()
        );

        // all angular templates should be public, they dont contains some private informations. 
        // all variables are parsed at the frontend
        $response->setPublic();
        $response->setSharedMaxAge(600);

        return $response;
    }
}