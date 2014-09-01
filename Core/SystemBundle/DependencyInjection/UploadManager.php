<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SystemBundle\DependencyInjection;

use \Symfony\Component\Security\Core\SecurityContextInterface;

class UploadManager extends \SymBB\Core\SystemBundle\DependencyInjection\AbstractManager
{

    protected $config = array();

    protected $rootDir;

    public function __construct($symbbConfig, $rootDir)
    {
        foreach ($symbbConfig['upload'] as $set => $config) {
            if (!\strpos($config['directory'], '/') === 0) {
                $config['directory'] = '/' . $config['directory'];
            }
            if (substr($config['directory'], -1) !== '/') {
                $config['directory'] = $config['directory'] . '/';
            }
            $this->config[$set] = $config;
        }
        $this->rootDir = $rootDir;
    }

    public function handleUpload(\Symfony\Component\HttpFoundation\Request $request, $set = 'tmp')
    {
        $fileData = array();
        if (isset($this->config[$set])) {
            $config = $this->config[$set];

            $files = $request->files;
            if (\is_object($files)) {
                foreach ($files as $file) {
                    $dir = $config['directory'] . $this->getCurrentSubDirectory();
                    $newDir = $this->addRootDir($dir);
                    $originalName = $file->getClientOriginalName();
                    $extData = \explode('.', $originalName);
                    $ext = end($extData);
                    $name = \uniqid(true) . '.' . $ext;
                    $file->move($newDir, $name);
                    $fileData[] = array(
                        'originalFilename' => $originalName,
                        'filename' => $name,
                        'url' => $dir . $name
                    );
                }
                return $fileData;
            } else {
                return $fileData;
            }
        }
        return $fileData;
    }

    public function addRootDir($path)
    {
        $rootPart = $this->rootDir . '/../web';
        if (\strpos($path, $rootPart) === false) {
            $path = $this->rootDir . '/../web' . $path;
        }
        
        return $path;
    }

    public function getCurrentSubDirectory()
    {
        $now = new \DateTime();
        return $now->format('Y-m') . '/';
    }

    public function moveToSet($set, $file)
    {
        if (isset($this->config[$set])) {
            $config = $this->config[$set];
            if (\strpos($file, $config['directory']) === false) {
                $temp = \explode('/', $file);
                $filename = end($temp);
                $newPath = $config['directory'] . $this->getCurrentSubDirectory();
                $this->checkDir($newPath);
                $newPath = $newPath . $filename;
                \rename($this->addRootDir($file), $this->addRootDir($newPath));
                return $newPath;
            }
        }
        return $file;
    }

    public function checkDir($dir){
        if(!is_dir($this->addRootDir($dir))){
            mkdir($this->addRootDir($dir));
        }
    }
}