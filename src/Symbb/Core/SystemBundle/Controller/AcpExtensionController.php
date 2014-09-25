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

class AcpExtensionController extends \Symbb\Core\SystemBundle\Controller\AbstractController
{
    
    public function indexAction(){
        
        $extensions = Api::getExtensions();
        
        return $this->render(
            $this->getTemplateBundleName('acp').':Acp:System\extensions.html.twig',
            array('extensions' => $extensions)
        );
        
    }
    
    public function enableAction($package){
        
        $package = \str_replace('|', '/', $package);
        
        
        $uri = $this->get('router')->generate('_symbbcoresystembundle_extensions');
        
        $api = new Api();
        $api->enable($package);
        $api->clearCache();
        
        //workaound because after api call the cache will be deleted and after this the normale rendering of an twig template wil throw errors
        echo '<meta http-equiv="refresh" content="0; URL='.$uri.'">';
        die();
        
    }
    
    public function disableAction($package){
        
        $package = \str_replace('|', '/', $package);
        
        $uri = $this->get('router')->generate('_symbbcoresystembundle_extensions');
        
        $api = new Api();
        $api->disable($package);
        $api->clearCache();
        
        //workaound because after api call the cache will be deleted and after this the normale rendering of an twig template wil throw errors
        echo '<meta http-equiv="refresh" content="0; URL='.$uri.'">';
        die();
        
    }
}