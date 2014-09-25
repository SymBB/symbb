<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\EventBundle\Event;


class ApiSaveEvent extends \Symbb\Core\EventBundle\Event\AbstractApiEvent
{


    protected $object;

    /**
     * @var array
     */
    protected $extensionData;

    public function __construct($object, $extensionData)
    {
        $this->object = $object;
        $this->extensionData = $extensionData;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function getExtensionData()
    {
        return $this->extensionData;
    }
}
