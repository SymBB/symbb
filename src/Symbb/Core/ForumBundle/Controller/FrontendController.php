<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Controller;

class FrontendController extends \Symbb\Core\SystemBundle\Controller\AbstractController
{

    public function indexAction()
    {
        return $this->portalAction();
    }

    public function portalAction()
    {
        return $this->render($this->getTemplateBundleName('forum') . ':Forum:index.html.twig', array());
    }

    public function forumAction()
    {
        return $this->render($this->getTemplateBundleName('forum') . ':Forum:index.html.twig', array());
    }

}