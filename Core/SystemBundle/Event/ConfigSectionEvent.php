<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SystemBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ConfigSectionEvent extends Event
{

    protected $key;

    protected $sections = array();

    public function __construct()
    {
        $this->sections["default"] = "default";
    }

    public function getKey()
    {
        return $this->key;
    }

    public function addSection($section)
    {
        $this->sections[$section] = $section;
    }

    public function getSections()
    {
        return $this->sections;
    }
}
