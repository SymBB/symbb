<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\AngularBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class RouterFilesEvent extends Event
{

    protected $files = array();

    public function __construct($files)
    {
        $this->files = $files;
    }

    public function addFile($file)
    {
        $this->files[] = $file;
    }

    public function getFiles()
    {
        return $this->files;
    }
}