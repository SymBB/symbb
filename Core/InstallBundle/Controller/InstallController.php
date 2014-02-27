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

    public function stepAction($step)
    {

        $kernel = $this->get('kernel');

        $output = array();
        $errors = array();

        if ($step == 2) {

            $executer = new CommandExecuter($kernel);
            $env = $kernel->getEnvironment();

            $result = $executer->execute('doctrine:schema:drop --force --em=symbb --env=' . $env);
            $output[] = $result['output'];

            $result = $executer->execute('doctrine:schema:update --force --em=symbb --env=' . $env);
            $output[] = $result['output'];

            $result = $executer->execute('init:acl --env=' . $env);
            $output[] = $result['output'];

            $result = $executer->execute('doctrine:fixtures:load --em=symbb --env=' . $env);
            $output[] = $result['output'];



            $result = $executer->execute('asset:install --env=' . $env);
            $output[] = $result['output'];

            $result = $executer->execute('assetic:dump --env=' . $env);
            $output[] = $result['output'];
        } else if ($step == 3) {
            $api = new \SymBB\ExtensionBundle\Api();

            if ($api->checkFileAccess()) {

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

                $extensionPostUpload = new \SymBB\ExtensionBundle\Extension();
                $extensionPostUpload->setName('SymBB Post Upload');
                $extensionPostUpload->setBundleClass('\SymBB\Extension\PostUploadBundle\SymBBExtensionPostUploadBundle');
                $extensionPostUpload->enable();
                $extensionPostUpload->setPackage('symbb/extension-post-upload');
                $extensionPostUpload->disableComposer();

                $extensionPostCalendar = new \SymBB\ExtensionBundle\Extension();
                $extensionPostCalendar->setName('SymBB Calendar');
                $extensionPostCalendar->setBundleClass('\SymBB\Extension\CalendarBundle\SymBBExtensionCalendarBundle');
                $extensionPostCalendar->enable();
                $extensionPostCalendar->setPackage('symbb/extension-calendar');
                $extensionPostCalendar->disableComposer();

                $extensionUserTag = new \SymBB\ExtensionBundle\Extension();
                $extensionUserTag->setName('SymBB User Tags');
                $extensionUserTag->setBundleClass('\SymBB\Extension\UserTagBundle\SymBBExtensionUserTagBundle');
                $extensionUserTag->enable();
                $extensionUserTag->setPackage('symbb/extension-user-tag');
                $extensionUserTag->disableComposer();

                $extensionTapatalk = new \SymBB\ExtensionBundle\Extension();
                $extensionTapatalk->setName('SymBB Tapatalk');
                $extensionTapatalk->setBundleClass('\SymBB\Extension\TapatalkBundle\SymBBExtensionTapatalkBundle');
                $extensionTapatalk->enable();
                $extensionTapatalk->setPackage('symbb/extension-tapatalk');
                $extensionTapatalk->disableComposer();
                
                
                $api->remove('symbb/extension-post-upload');
                $api->remove('symbb/extension-survey');
                $api->remove('symbb/extension-bbcode');
                $api->remove('symbb/extension-rating');
                $api->remove('symbb/extension-calendar');
                $api->remove('symbb/extension-user-tag');
                $api->remove('symbb/extension-tapatalk');
                $api->addExtension($extensionRating);
                $api->addExtension($extensionSurvey);
                $api->addExtension($extensionBBCode);
                $api->addExtension($extensionPostUpload);
                $api->addExtension($extensionPostCalendar);
                $api->addExtension($extensionUserTag);
                $api->addExtension($extensionTapatalk);
                
            } else {
                $errors[] = 'Permission denied (/app/config/extensions.yml)';
                $step = 2;
            }
        } else if ($step == 4) {
            $uri = $this->get('router')->generate('symbb_install_step', array('step' => 99));
            $api = new \SymBB\ExtensionBundle\Api();
            $api->clearCache();
            echo '<meta http-equiv="refresh" content="0; URL=' . $uri . '">';
            die();
        }

        foreach ($output as $key => $value) {
            $value = \str_replace('color:rgba(230,230,50,1)', 'color:rgba(0, 126, 5, 1)', $value);
            $output[$key] = \nl2br($value);
        }


        return $this->render(
            $this->getTemplateBundleName('forum') . ':Install:step_' . $step . '.html.twig', array('step' => $step, 'output' => $output, 'errors' => $errors)
        );

    }
}