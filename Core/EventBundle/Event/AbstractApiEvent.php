<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\EventBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class AbstractApiEvent extends Event
{

    protected $messages = array();

    protected $callbacks = array();

    public function getMessages()
    {
        return $this->messages;
    }

    public function getCallbacks()
    {
        return $this->callbacks;
    }

    protected function addErrorMessage($message)
    {
        $this->messages[] = array(
            'type' => 'error',
            'bootstrapType' => 'danger',
            'message' => $this->trans($message)
        );
        $this->success = false;
    }

    protected function addSuccessMessage($message)
    {
        $this->messages[] = array(
            'type' => 'success',
            'bootstrapType' => 'success',
            'message' => $this->trans($message)
        );
    }

    protected function addInfoMessage($message)
    {
        $this->messages[] = array(
            'type' => 'info',
            'bootstrapType' => 'info',
            'message' => $this->trans($message)
        );
    }

    protected function addWarningMessage($message)
    {
        $this->messages[] = array(
            'type' => 'warning',
            'bootstrapType' => 'warning',
            'message' => $this->trans($message)
        );
    }

    protected function addCallback($callbackName)
    {
        $this->callbacks[] = $callbackName;
    }
}
