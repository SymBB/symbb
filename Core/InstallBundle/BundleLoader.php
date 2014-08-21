<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\InstallBundle;

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
            // SymBB, befor other Bundles
            '\SymBB\Core\ConfigBundle\SymBBCoreConfigBundle',
            '\SymBB\Core\UserBundle\SymBBCoreUserBundle',
            '\SymBB\Core\AdminBundle\SymBBCoreAdminBundle',
            '\SymBB\Core\ForumBundle\SymBBCoreForumBundle',
            '\SymBB\Core\SiteBundle\SymBBCoreSiteBundle',
            '\SymBB\Core\SystemBundle\SymBBCoreSystemBundle',
            '\SymBB\Core\InstallBundle\SymBBCoreInstallBundle',
            '\SymBB\Core\EventBundle\SymBBCoreEventBundle',
            '\SymBB\Core\BBCodeBundle\SymBBCoreBBCodeBundle',
            '\SymBB\Core\AngularBundle\SymBBCoreAngularBundle',
            '\SymBB\Core\MessageBundle\SymBBCoreMessageBundle',
            // SymBB optional bundles
            '\SymBB\FOS\UserBundle\SymBBFOSUserBundle',
            '\SymBB\ExtensionBundle\SymBBExtensionBundle',
            // SymBB Templates
            '\SymBB\Template\DefaultBundle\SymBBTemplateDefaultBundle',
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
            '\Vich\UploaderBundle\VichUploaderBundle',
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

        \SymBB\ExtensionBundle\KernelPlugin::addBundles($bundles);
    }
}