<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\InstallBundle;

class BundleLoader
{

    public static function loadBundles(&$bundles, $kernel)
    {

        $symbbBundles = array(
            '\Symfony\Bundle\FrameworkBundle\FrameworkBundle',
            '\Symfony\Bundle\SecurityBundle\SecurityBundle',
            '\Symfony\Bundle\TwigBundle\TwigBundle',
            '\Symfony\Bundle\MonologBundle\MonologBundle',
            '\Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle',
            '\Symfony\Bundle\AsseticBundle\AsseticBundle',
            '\Doctrine\Bundle\DoctrineBundle\DoctrineBundle',
            '\Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle',
            '\JMS\DiExtraBundle\JMSDiExtraBundle',
            '\JMS\SecurityExtraBundle\JMSSecurityExtraBundle',
            '\JMS\SerializerBundle\JMSSerializerBundle',
            '\JMS\AopBundle\JMSAopBundle',
            // none Default Bundles
            '\Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle',
            // Symbb, befor other Bundles
            '\Symbb\Core\ConfigBundle\SymbbCoreConfigBundle',
            '\Symbb\Core\UserBundle\SymbbCoreUserBundle',
            '\Symbb\Core\AdminBundle\SymbbCoreAdminBundle',
            '\Symbb\Core\ForumBundle\SymbbCoreForumBundle',
            '\Symbb\Core\SiteBundle\SymbbCoreSiteBundle',
            '\Symbb\Core\SystemBundle\SymbbCoreSystemBundle',
            '\Symbb\Core\InstallBundle\SymbbCoreInstallBundle',
            '\Symbb\Core\EventBundle\SymbbCoreEventBundle',
            '\Symbb\Core\BBCodeBundle\SymbbCoreBBCodeBundle',
            '\Symbb\Core\AngularBundle\SymbbCoreAngularBundle',
            '\Symbb\Core\MessageBundle\SymbbCoreMessageBundle',
            '\Symbb\Core\NewsBundle\SymbbCoreNewsBundle',
            // Symbb optional bundles
            '\Symbb\FOS\UserBundle\SymbbFOSUserBundle',
            '\Symbb\ExtensionBundle\SymbbExtensionBundle',
            // Symbb Templates
            '\Symbb\Template\DefaultBundle\SymbbTemplateDefaultBundle',
            // FOS 
            '\FOS\UserBundle\FOSUserBundle',
            '\FOS\JsRoutingBundle\FOSJsRoutingBundle',
            // KNP
            '\Knp\Bundle\MenuBundle\KnpMenuBundle',
            '\Knp\Bundle\PaginatorBundle\KnpPaginatorBundle',
            '\Knp\Bundle\GaufretteBundle\KnpGaufretteBundle',
            // Sonata
            '\Sonata\IntlBundle\SonataIntlBundle',
            //
            '\Lsw\MemcacheBundle\LswMemcacheBundle',
            '\JMS\TranslationBundle\JMSTranslationBundle',
            '\Liip\ImagineBundle\LiipImagineBundle',
            //important! need for json post request from angular
            '\FOS\RestBundle\FOSRestBundle',
        );

        foreach ($symbbBundles as $symbbBundle) {
            $found = false;
            foreach ($bundles as $bundle) {
                if ($bundle instanceof $symbbBundle) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                if ($symbbBundle === '\JMS\DiExtraBundle\JMSDiExtraBundle') {
                    $bundles[] = new $symbbBundle($kernel);
                } else {
                    $bundles[] = new $symbbBundle();
                }
            }
        }

        if (in_array($kernel->getEnvironment(), array('dev', 'test'))) {
            //$bundles[] = new \CoreSphere\ConsoleBundle\CoreSphereConsoleBundle();
        }

        \Symbb\ExtensionBundle\KernelPlugin::addBundles($bundles);
    }
}