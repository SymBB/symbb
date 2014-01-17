<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\InstallBundle\Controller;

use CoreSphere\ConsoleBundle\Executer\CommandExecuter;

class InstallController extends \SymBB\Core\SystemBundle\Controller\AbstractController 
{

    
    public function stepAction($step){
        
        $kernel     = $this->get('kernel');
        
        $output     = array();
        $errors     = array();
        
        if($step == 2){
            
            $executer   = new CommandExecuter($kernel);
            $env        = $kernel->getEnvironment();
            
            $result     = $executer->execute('doctrine:schema:drop --force --em=symbb --env='.$env);
            $output[]   = $result['output'];
            
            $result     = $executer->execute('doctrine:schema:update --force --em=symbb --env='.$env);
            $output[]   = $result['output'];
            
            $result     = $executer->execute('init:acl --env='.$env);
            $output[]   = $result['output'];
            
            $result     = $executer->execute('doctrine:fixtures:load --em=symbb --env='.$env);
            $output[]   = $result['output'];
            
            
            
            $result     = $executer->execute('asset:install --env='.$env);
            $output[]   = $result['output'];
            
            $result     = $executer->execute('assetic:dump --env='.$env);
            $output[]   = $result['output'];
            
            
        } else if($step == 3){
            $api        = new \SymBB\ExtensionBundle\Api();
        
            if($api->checkFileAccess()){
            
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

                $api->addExtension($extensionRating);
                $api->addExtension($extensionSurvey);
                $api->addExtension($extensionBBCode);
                
            } else {
                $errors[] = 'Permission denied (/app/config/extensions.yml)';
                $step = 2;
            }
        } else if($step == 4){
            $uri = $this->get('router')->generate('symbb_install_step', array('step' => 99));
            $api        = new \SymBB\ExtensionBundle\Api();
            $api->clearCache();
            echo '<meta http-equiv="refresh" content="0; URL='.$uri.'">';
            die();
        }
        
        foreach($output as $key => $value){
            $value        = \str_replace('color:rgba(230,230,50,1)', 'color:rgba(0, 126, 5, 1)', $value);
            $output[$key] = \nl2br($value);
        }
        
        
        return $this->render(
            $this->getTemplateBundleName('forum').':Install:step_'.$step.'.html.twig',
            array('step' => $step, 'output' => $output, 'errors' => $errors)
        );
    }
}