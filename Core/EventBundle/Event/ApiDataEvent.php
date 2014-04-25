<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\EventBundle\Event;

class ApiDataEvent extends \SymBB\Core\EventBundle\Event\AbstractApiEvent
{

    protected $object;

    /**
     * @var array
     */
    protected $extensionData;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function getExtensionData()
    {
        return $this->extensionData;
    }

    public function addExtensionData($key, $data)
    {
        $this->extensionData[$key] = $data;
    }
}
