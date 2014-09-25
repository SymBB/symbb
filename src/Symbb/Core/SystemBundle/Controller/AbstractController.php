<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SystemBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class AbstractController extends Controller
{

    protected $templateBundle;

    protected function getTemplateBundleName($for = 'forum')
    {

        $WIOmanager = $this->get('symbb.core.user.whoisonline.manager');
        $WIOmanager->addCurrent($this->get('request'));

        if ($this->templateBundle === null) {
            $this->templateBundle = $this->container->get('symbb.core.site.manager')->getTemplate($for);
        }

        return $this->templateBundle;
    }
}