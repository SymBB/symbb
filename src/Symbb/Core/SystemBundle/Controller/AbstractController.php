<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SystemBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

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

    public function addSuccess($message, $request)
    {
        $request->getSession()->getFlashBag()->add(
            'success',
            $message
        );
    }

    public function addError($message, $request)
    {
        $request->getSession()->getFlashBag()->add(
            'error',
            $message
        );
    }

    public function addInfo($message, $request)
    {
        $request->getSession()->getFlashBag()->add(
            'notice',
            $message
        );
    }

    public function returnToLastPage($request)
    {
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }

    public function addSavedSuccess(Request $request){
        $message = $this->get("translator")->trans("Saved successful.", array(), "symbb_frontend");
        $this->addSuccess($message, $request);
    }
}