<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\InstallBundle;

class BundleLoader {
    
    public static function loadBundles(&$bundles){
        
        $symbbBundles = array(
                // none Default Bundles
                new \Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
                // SymBB, befor other Bundles
                new \SymBB\Core\ConfigBundle\SymBBCoreConfigBundle(),
                new \SymBB\Core\UserBundle\SymBBCoreUserBundle(),
                new \SymBB\Core\AdminBundle\SymBBCoreAdminBundle(),
                new \SymBB\Core\ForumBundle\SymBBCoreForumBundle(),
                new \SymBB\Core\SystemBundle\SymBBCoreSystemBundle(),
                new \SymBB\Core\InstallBundle\SymBBCoreInstallBundle(),
                new \SymBB\Core\EventBundle\SymBBCoreEventBundle(),
                new \SymBB\Core\MessageBundle\SymBBCoreMessageBundle(),
                // SymBB optional bundles
                new \SymBB\FOS\UserBundle\SymBBFOSUserBundle(),
                // SymBB Templates
                new \SymBB\Template\SimpleBundle\SymBBTemplateSimpleBundle(),
                // FOS 
                new \FOS\UserBundle\FOSUserBundle(),
                new \FOS\RestBundle\FOSRestBundle(),
                new \FOS\JsRoutingBundle\FOSJsRoutingBundle(),
                new \FOS\MessageBundle\FOSMessageBundle(),
                // KNP
                new \Knp\Bundle\MenuBundle\KnpMenuBundle(),
                new \Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
                // Sonata
                new \Sonata\IntlBundle\SonataIntlBundle(),
                //
                new \FM\BbcodeBundle\FMBbcodeBundle(),
                new \Lsw\MemcacheBundle\LswMemcacheBundle(),
            );
        
        $bundles = array_merge($bundles, $symbbBundles);
        
        \SymBB\ExtensionBundle\KernelPlugin::addBundles($bundles);
    }
    
}