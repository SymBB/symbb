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

    public function __construct(SecurityContextInterface $securityContext, $symbbConfig, $rootDir)
    {
        parent::__construct($securityContext);
        $this->config = $symbbConfig['upload'];
        $this->rootDir = $rootDir;
    }

    public function handleUpload(\Symfony\Component\HttpFoundation\Request $request, $set = 'tmp')
    {
        $fileData = array();
        if (isset($this->config[$set])) {
            $config = $this->config[$set];
            if (!\strpos($config['directory'], '/') === 0) {
                $config['directory'] = '/' . $config['directory'];
            }
            if (substr($config['directory'], -1)  !== '/') {
                $config['directory'] = $config['directory'] . '/';
            }
            $files = $request->files;
            if (\is_object($files)) {
                foreach ($files as $file) {
                    $now = new \DateTime();
                    $dir = $config['directory'] . $now->format('Y-m') . '/';
                    $newDir = $this->rootDir . '/../web' . $dir;
                    $originalName = $file->getClientOriginalName();
                    $extData = \explode('.', $originalName);
                    $ext = end($extData);
                    $name = \uniqid(true) . '.' . $ext;
                    $file->move($newDir, $name);
                    $fileData[] = array(
                        'originalFilename' => $originalName,
                        'filename' => $name,
                        'url' => $dir.$name
                    );
                }
                return $fileData;
            } else {
                return $fileData;
            }
        }
        return $fileData;
    }
}