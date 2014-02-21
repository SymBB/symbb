<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\InstallBundle\Composer;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;
use Composer\Script\CommandEvent;

/**
 * 
 */
class ScriptHandler
{
 
    public static function composerInstall(CommandEvent $event)
    {

        $options = self::getOptions($event);
        $appDir = $options['symfony-app-dir'];

        if (!is_dir($appDir)) {
            $event->getIO->write(sprintf('The symfony-app-dir (%s) specified in composer.json was not found in %s, can not clear the cache.', $appDir, getcwd()));

            return;
        }

        static::executeCommand($event, $appDir, 'doctrine:schema:update --force --em=symbb --env=prod', $options['process-timeout']);
        static::executeCommand($event, $appDir, 'doctrine:schema:update --force --em=symbb --env=dev', $options['process-timeout']);
        
        static::executeCommand($event, $appDir, 'init:acl --em=symbb --env=prod', $options['process-timeout']);
        static::executeCommand($event, $appDir, 'init:acl --em=symbb --env=dev', $options['process-timeout']);
        
        static::executeCommand($event, $appDir, 'doctrine:fixtures:load --env=prod', $options['process-timeout']);
        static::executeCommand($event, $appDir, 'doctrine:fixtures:load --env=dev', $options['process-timeout']);
        
        static::executeCommand($event, $appDir, 'cache:clear --env=prod', $options['process-timeout']);
        static::executeCommand($event, $appDir, 'cache:clear --env=dev', $options['process-timeout']);
        
        static::executeCommand($event, $appDir, 'assetic:dump --env=prod', $options['process-timeout']);
    }
    
    public static function composerUpdate(CommandEvent $event)
    {
        $options = self::getOptions($event);
        $appDir = $options['symfony-app-dir'];

        if (!is_dir($appDir)) {
            $event->getIO->write(sprintf('The symfony-app-dir (%s) specified in composer.json was not found in %s, can not clear the cache.', $appDir, getcwd()));

            return;
        }

        static::executeCommand($event, $appDir, 'cache:clear --env=prod', $options['process-timeout']);
        static::executeCommand($event, $appDir, 'assetic:dump --env=prod', $options['process-timeout']);
        //todo doctrine migrations
    }

    protected static function executeCommand(CommandEvent $event, $appDir, $cmd, $timeout = 300)
    {
        $php = escapeshellarg(self::getPhp());
        $console = escapeshellarg($appDir.'/console');
        if ($event->getIO()->isDecorated()) {
            $console .= ' --ansi';
        }

        $process = new Process($php.' '.$console.' '.$cmd, null, null, null, $timeout);
        $process->run(function ($type, $buffer) use ($event) { $event->getIO()->write($buffer, false); });
        if (!$process->isSuccessful()) {
            throw new \RuntimeException(sprintf('An error occurred when executing the "%s" command.', escapeshellarg($cmd)));
        }
    }

    protected static function getOptions(CommandEvent $event)
    {
        $options = array_merge(array(
            'symfony-app-dir' => 'app',
            'symfony-web-dir' => 'web',
            'symfony-assets-install' => 'hard'
        ), $event->getComposer()->getPackage()->getExtra());

        $options['symfony-assets-install'] = getenv('SYMFONY_ASSETS_INSTALL') ?: $options['symfony-assets-install'];

        $options['process-timeout'] = $event->getComposer()->getConfig()->get('process-timeout');

        return $options;
    }

    protected static function getPhp()
    {
        $phpFinder = new PhpExecutableFinder;
        if (!$phpPath = $phpFinder->find()) {
            throw new \RuntimeException('The php executable could not be found, add it to your PATH environment variable and try again');
        }

        return $phpPath;
    }
}