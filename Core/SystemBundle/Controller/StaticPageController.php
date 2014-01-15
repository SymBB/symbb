<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\SystemBundle\Controller;


class StaticPageController extends \SymBB\Core\SystemBundle\Controller\AbstractController 
{
    
    public function imprintAction(){
        
        $configData = $this->get('symbb.core.config.manager')->get('system.imprint');
        
        return $this->render(
            $this->getTemplateBundleName('forum').'::imprint.html.twig',
            array('imprint' => $configData)
        );
        
    }
    
    public function termsAction(){
        
        $configData = $this->get('symbb.core.config.manager')->get('system.terms');
        
        return $this->render(
            $this->getTemplateBundleName('forum').'::terms.html.twig',
            array('imprint' => $configData)
        );
        
    }
}