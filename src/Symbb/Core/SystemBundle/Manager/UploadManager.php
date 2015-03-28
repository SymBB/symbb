<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SystemBundle\Manager;

use \Symfony\Component\Security\Core\SecurityContextInterface;
use \Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadManager extends \Symbb\Core\SystemBundle\Manager\AbstractManager
{

    protected $config = array();

    protected $rootDir;

    /**
     * 5mb (5 * 1024 * 1024)
     */
    const MAX_FILE_SIZE = 5242880;

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

    /**
     * @param $file
     * @return bool
     */
    protected function validFileType(UploadedFile $file){
        $originalName = $file->getClientOriginalName();
        $type = explode(".", $originalName);
        $type = end($type);
        if(in_array($type, array(
            'png', 'jpg', 'gif'
        ))){
            return true;
        }
        return false;
    }

    /**
     * @param UploadedFile $file
     * @return bool
     */
    protected function validSize(UploadedFile $file){
        $size = $file->getClientSize();
        if($size > self::MAX_FILE_SIZE){
            return false;
        }
        return true;
    }

    public function handleUpload(\Symfony\Component\HttpFoundation\Request $request, $set = 'tmp')
    {
        $fileData = array();
        if (isset($this->config[$set])) {
            $config = $this->config[$set];
            $files = $request->files;
            $errors = array();
            if (\is_object($files)) {
                foreach ($files as $file) {
                    if(!$file->isValid()){
                        $errors[] = $this->getFileErrorData($file, "Uploading was throwing an error %error%", array("%error%" => $file->getError()));
                    } else if(!$this->validFileType($file)) {
                        $errors[] = $this->getFileErrorData($file, "FileType not allowed", array());
                    } else if(!$this->validSize($file)){
                        $errors[] = $this->getFileErrorData($file, "File is to big", array());
                    }
                }
                if(empty($errors)){
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
                } else {
                    $fileData = $errors;
                }
            }
        }
        return $fileData;
    }

    /**
     * @param UploadedFile $file
     * @param $message
     * @param array $transParams
     * @return array
     */
    protected function getFileErrorData(UploadedFile $file, $message, $transParams = array()){
        $fileData = array(
            'originalFilename' => $file->getClientOriginalName(),
            'filename' => $file->getClientOriginalName(),
            'url' => null,
            'error' => $this->translator->trans($message, $transParams, "symbb_frontend")
        );
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

    public function checkDir($dir)
    {
        if (!is_dir($this->addRootDir($dir))) {
            mkdir($this->addRootDir($dir));
        }
    }
}