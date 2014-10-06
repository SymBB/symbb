<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\TapatalkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class XmlrpcController extends Controller
{

    public function indexAction()
    {
        $server = new \Zend\XmlRpc\Server;
        $server->setReturnResponse(true);
        $server->setClass($this->get('symbb.extension.tapatalk.manager.call'));
        $responseZend = $server->handle();

        // error case
        if ($responseZend instanceof \Zend\XmlRpc\Fault) {
            $sfResponse = new \Symfony\Component\HttpFoundation\Response();
            $sfResponse->headers->set('Content-Type', 'text/xml; charset=UTF-8');
            $sfResponse->setContent($responseZend->saveXml());
        } else {
            $this->get('monolog.logger.tapatalk')->debug($this->get('request'));
            $sfResponse = $responseZend->getReturnValue();
        }

        $sfResponse = $this->addResponseHeader($sfResponse);

        return $sfResponse;
    }

    public function avatarAction()
    {

        $userId = (int) $this->get('request')->get('user_id');
        $username = (string) $this->get('request')->get('username');

        if ($userId > 0) {
            $user = $this->get('symbb.core.user.manager')->find($userId);
        } else {
            $user = $this->get('symbb.core.user.manager')->findByUsername($username);
        }

        if (!$user) {
            $user = new \Symbb\Core\UserBundle\Entity\User();
        }

        $avatar = $user->getAvatar();
        $root = $this->get('kernel')->getWebDir();
        $avatar = $root . $avatar;

        $fp = fopen($avatar, "rb");
        $str = stream_get_contents($fp);
        fclose($fp);

        $response = new Response($str, 200);

        if (\strpos('.png', $avatar)) {
            $response->headers->set('Content-Type', 'image/png');
        } else if (\strpos('.gif', $avatar)) {
            $response->headers->set('Content-Type', 'image/gif');
        } else {
            $response->headers->set('Content-Type', 'image/jpg');
        }

        return $response;
    }

    public function testAction()
    {
        $response = $this->get('symbb.extension.tapatalk.manager.call')->login("User", "User123");
        return $response;
    }

    protected function addResponseHeader($sfResponse)
    {

        $user = $this->get('symbb.core.user.manager')->getCurrentUser();
        if ($user->getSymbbType() === 'user') {
            $sfResponse->headers->set('Mobiquo_is_login', true);
        }

        return $sfResponse;
    }
}