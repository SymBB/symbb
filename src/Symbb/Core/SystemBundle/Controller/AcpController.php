<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Symbb\Core\SystemBundle\Controller;

use Symbb\ExtensionBundle\Api;
use \Symfony\Component\HttpFoundation\Response;

class AcpController extends \Symbb\Core\SystemBundle\Controller\AbstractController
{

    public function indexAction()
    {
        return $this->render(
            $this->getTemplateBundleName('acp') . ':AcpAngular:index.html.twig',
            array()
        );

    }

}