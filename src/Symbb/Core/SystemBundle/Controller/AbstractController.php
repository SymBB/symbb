<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SystemBundle\Controller;

use Symbb\Core\ForumBundle\Entity\Forum;
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

    /**
     * @return array|mixed
     */
    public function getForumTreeArray(){
        $data = array();
        $forums = $this->get("symbb.core.forum.manager")->findAll(null, 999, 1, false);
        foreach($forums as $forum){
            $data = $this->addForumToTreeArray($forum, $data);
        }
        return $data;
    }

    /**
     * @param Forum $forum
     * @param $data
     * @param string $prefix
     * @return mixed
     */
    protected function addForumToTreeArray(Forum $forum, $data, $prefix = ""){
        $name = $forum->getName();
        if(!empty($prefix)){
            $name = $prefix." - ".$name;
        }
        $data[] =  array("id" => $forum->getId(), "name" => $name, "type" => $forum->getType());
        foreach($forum->getChildren() as $subforum){
            $data = $this->addForumToTreeArray($subforum, $data, $name);
        }
        return $data;
    }
}