<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SystemBundle\DependencyInjection;


class UpdateManager
{

    private $kernel;
    
    private $data;
    
    private $symbbData;

    /**
     * Class constructor
     *
     * @param KernelInterface $kernel Kernel object
     */
    public function __construct(\Symfony\Component\HttpKernel\KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function collect()
    {

        $rootDir = realpath($this->kernel->getRootDir() . '/../');
        $installed = json_decode(file_get_contents($rootDir.'/composer.lock'), true);
        $require = json_decode(file_get_contents($rootDir.'/composer.json'));
        $require = (array)$require->require;
        $lastUpdate = filemtime($rootDir.'/app/SymfonyRequirements.php');
        $packages = array();
        $packageCount=0;
        $unstablePackageCount=0;
        foreach ($installed['packages'] as $package)
        {
            
            $name = $package["name"];
            if(isset($package["description"])){
                $description = $package["description"];
            }
            $version = $package["source"]["reference"];
            $required = isset($require[$name])?$require[$name]:'-';
            $unstable = strlen($version)==40;
            $packages[] = compact('name','required','version','unstable','description');
            // update counters
            $unstablePackageCount+=$unstable;
            $packageCount++;
        }

        $this->data = compact('lastUpdate','packages','packageCount','unstablePackageCount');
        $this->symbbData = array();
        foreach($this->data["packages"] as $pakage){
            if(\strpos($pakage["name"], 'symbb') !== false){
                $this->symbbData[] = $pakage;
            }
        }
        
        return $this->data;
    }
    
    public function getSymbbData(){
        return $this->symbbData;
    }

    /**
     * Method returns date of last update
     *
     * @return number
     */
    public function getLastUpdate()
    {
        return $this->data['lastUpdate'];
    }


    /**
     * Method returns days since last update
     *
     * @return number
     */
    public function getDays()
    {
        return round((time()-$this->data['lastUpdate'])/86400);
    }


    /**
     * Method returns amount of installed packages
     *
     * @return number
     */
    public function getPackageCount()
    {
        return $this->data['packageCount'];
    }

    /**
     * Method returns amount of installed unstable packages
     *
     * @return number
     */
    public function getUnstablePackageCount()
    {
        return $this->data['unstablePackageCount'];
    }

    /**
     * Method returns the installed packages
     *
     * @return number
     */
    public function getPackages()
    {
        return $this->data['packages'];
    }


}