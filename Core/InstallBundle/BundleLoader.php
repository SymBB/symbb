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
            // none Default Bundles
            '\Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle',
            // SymBB, befor other Bundles
            '\SymBB\Core\ConfigBundle\SymBBCoreConfigBundle',
            '\SymBB\Core\UserBundle\SymBBCoreUserBundle',
            '\SymBB\Core\AdminBundle\SymBBCoreAdminBundle',
            '\SymBB\Core\ForumBundle\SymBBCoreForumBundle',
            '\SymBB\Core\SystemBundle\SymBBCoreSystemBundle',
            '\SymBB\Core\InstallBundle\SymBBCoreInstallBundle',
            '\SymBB\Core\EventBundle\SymBBCoreEventBundle',
            '\SymBB\Core\MessageBundle\SymBBCoreMessageBundle',
            // SymBB optional bundles
            '\SymBB\FOS\UserBundle\SymBBFOSUserBundle',
            '\SymBB\ExtensionBundle\SymBBExtensionBundle',
            // SymBB Templates
            '\SymBB\Template\DefaultBundle\SymBBTemplateDefaultBundle',
            '\SymBB\Template\TestBundle\SymBBTemplateTestBundle',
            // FOS 
            '\FOS\UserBundle\FOSUserBundle',
            '\FOS\RestBundle\FOSRestBundle',
            '\FOS\JsRoutingBundle\FOSJsRoutingBundle',
            '\FOS\MessageBundle\FOSMessageBundle',
            // KNP
            '\Knp\Bundle\MenuBundle\KnpMenuBundle',
            '\Knp\Bundle\PaginatorBundle\KnpPaginatorBundle',
            '\Knp\Bundle\GaufretteBundle\KnpGaufretteBundle',
            // Sonata
            '\Sonata\IntlBundle\SonataIntlBundle',
            //
            '\FM\BbcodeBundle\FMBbcodeBundle',
            '\Lsw\MemcacheBundle\LswMemcacheBundle',
            '\JMS\TranslationBundle\JMSTranslationBundle',
            '\Vich\UploaderBundle\VichUploaderBundle',
            '\Liip\ImagineBundle\LiipImagineBundle'
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
                $bundles[] = new $symbbBundle();
            }
        }

        if (in_array($kernel->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new \CoreSphere\ConsoleBundle\CoreSphereConsoleBundle();
        }

        \SymBB\ExtensionBundle\KernelPlugin::addBundles($bundles);
    }
}