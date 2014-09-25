<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\FOS\UserBundle\Controller;

class RegistrationController extends \FOS\UserBundle\Controller\RegistrationController
{

    public function registerAction(\Symfony\Component\HttpFoundation\Request $request)
    {

        $enabled = $this->container->get('symbb.core.config.manager')->get('system.registration.enabled', "default");

        if ($enabled) {
            return parent::registerAction($request);
        } else {

            $template = $this->getTemplateBundleName();

            return $this->container->get('templating')->renderResponse($template . ':Registration:disabled.html.' . $this->getEngine(), array(
            ));
        }
    }

    protected $templateBundle;

    protected function getTemplateBundleName($for = 'forum')
    {

        if ($this->templateBundle === null) {
            $this->templateBundle = $this->container->get('symbb.core.site.manager')->getTemplate($for);
        }

        return $this->templateBundle;
    }
}