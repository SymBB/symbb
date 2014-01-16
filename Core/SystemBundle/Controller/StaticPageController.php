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
    
    public function testAction(){
        
        $api = new \SymBB\ExtensionBundle\Api();
        
        $extensionRating = new \SymBB\ExtensionBundle\Extension();
        $extensionRating->setName('SymBB Rating');
        $extensionRating->setBundleClass('\SymBB\Extension\RatingBundle\SymBBExtensionRatingBundle');
        $extensionRating->enable();
        $extensionRating->setPackage('symbb/extension-rating');
        $extensionRating->disableComposer();
        
        $extensionBBCode = new \SymBB\ExtensionBundle\Extension();
        $extensionBBCode->setName('SymBB BBCode');
        $extensionBBCode->setBundleClass('\SymBB\Extension\BBCodeBundle\SymBBExtensionBBCodeBundle');
        $extensionBBCode->enable();
        $extensionBBCode->setPackage('symbb/extension-bbcode');
        $extensionBBCode->disableComposer();
        
        $extensionSurvey = new \SymBB\ExtensionBundle\Extension();
        $extensionSurvey->setName('SymBB Survey');
        $extensionSurvey->setBundleClass('\SymBB\Extension\SurveyBundle\SymBBExtensionSurveyBundle');
        $extensionSurvey->enable();
        $extensionSurvey->setPackage('symbb/extension-survey');
        $extensionSurvey->disableComposer();
        
        $api->add('https://github.com/FriendsOfSymfony/FOSUserBundle', 'dev-master');
        
        $api->addExtension($extensionRating);
        $api->addExtension($extensionSurvey);
        $api->addExtension($extensionBBCode);
        
        return $this->termsAction(); 
        
    }
}