<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\InstallBundle;

abstract class Kernel extends \Symfony\Component\HttpKernel\Kernel
{
    
    public function registerBundles()
    {
        $bundles = array();
        \Symbb\Core\InstallBundle\BundleLoader::loadBundles($bundles, $this);
        return $bundles;
    }


    /**
     * Initializes the data structures related to the bundle management.
     *
     *  - the bundles property maps a bundle name to the bundle instance,
     *  - the bundleMap property maps a bundle name to the bundle inheritance hierarchy (most derived bundle first).
     *
     * @throws \LogicException if two bundles share a common name
     * @throws \LogicException if a bundle tries to extend a non-registered bundle
     * @throws \LogicException if a bundle tries to extend itself
     * @throws \LogicException if two bundles extend the same ancestor
     */
    protected function initializeBundles()
    {
        parent::initializeBundles();

        foreach ($this->registerBundles() as $bundle) {

            if (\method_exists($bundle, "extendBundle")) {
                $name = $bundle->getName();
                $extenBundle = $bundle->extendBundle();
                $this->bundleMap[$name][] = $this->bundles[$extenBundle];
            }
        }
    }
    
}
