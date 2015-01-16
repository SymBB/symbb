<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\InstallBundle\Controller;

use CoreSphere\ConsoleBundle\Executer\CommandExecuter;

class InstallController extends \Symbb\Core\SystemBundle\Controller\AbstractController
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

            $result = $executer->execute('doctrine:fixtures:load --em=symbb --env=' . $env);
            $output[] = $result['output'];


            $result = $executer->execute('asset:install --env=' . $env);
            $output[] = $result['output'];

            $result = $executer->execute('assetic:dump --env=' . $env);
            $output[] = $result['output'];
        } else if ($step == 3) {
            $api = new \Symbb\ExtensionBundle\Api();

            if ($api->checkFileAccess()) {

                $extensionRating = new \Symbb\ExtensionBundle\Extension();
                $extensionRating->setName('Symbb Rating');
                $extensionRating->setBundleClass('\Symbb\Extension\RatingBundle\SymbbExtensionRatingBundle');
                $extensionRating->enable();
                $extensionRating->setPackage('symbb/extension-rating');
                $extensionRating->disableComposer();

                $extensionBBCode = new \Symbb\ExtensionBundle\Extension();
                $extensionBBCode->setName('Symbb BBCode');
                $extensionBBCode->setBundleClass('\Symbb\Extension\BBCodeBundle\SymbbExtensionBBCodeBundle');
                $extensionBBCode->enable();
                $extensionBBCode->setPackage('symbb/extension-bbcode');
                $extensionBBCode->disableComposer();

                $extensionSurvey = new \Symbb\ExtensionBundle\Extension();
                $extensionSurvey->setName('Symbb Survey');
                $extensionSurvey->setBundleClass('\Symbb\Extension\SurveyBundle\SymbbExtensionSurveyBundle');
                $extensionSurvey->enable();
                $extensionSurvey->setPackage('symbb/extension-survey');
                $extensionSurvey->disableComposer();

                $extensionPostUpload = new \Symbb\ExtensionBundle\Extension();
                $extensionPostUpload->setName('Symbb Post Upload');
                $extensionPostUpload->setBundleClass('\Symbb\Extension\PostUploadBundle\SymbbExtensionPostUploadBundle');
                $extensionPostUpload->enable();
                $extensionPostUpload->setPackage('symbb/extension-post-upload');
                $extensionPostUpload->disableComposer();

                $extensionPostCalendar = new \Symbb\ExtensionBundle\Extension();
                $extensionPostCalendar->setName('Symbb Calendar');
                $extensionPostCalendar->setBundleClass('\Symbb\Extension\CalendarBundle\SymbbExtensionCalendarBundle');
                $extensionPostCalendar->enable();
                $extensionPostCalendar->setPackage('symbb/extension-calendar');
                $extensionPostCalendar->disableComposer();

                $extensionUserTag = new \Symbb\ExtensionBundle\Extension();
                $extensionUserTag->setName('Symbb User Tags');
                $extensionUserTag->setBundleClass('\Symbb\Extension\UserTagBundle\SymbbExtensionUserTagBundle');
                $extensionUserTag->enable();
                $extensionUserTag->setPackage('symbb/extension-user-tag');
                $extensionUserTag->disableComposer();

                $extensionTapatalk = new \Symbb\ExtensionBundle\Extension();
                $extensionTapatalk->setName('Symbb Tapatalk');
                $extensionTapatalk->setBundleClass('\Symbb\Extension\TapatalkBundle\SymbbExtensionTapatalkBundle');
                $extensionTapatalk->enable();
                $extensionTapatalk->setPackage('symbb/extension-tapatalk');


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
            $api = new \Symbb\ExtensionBundle\Api();
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