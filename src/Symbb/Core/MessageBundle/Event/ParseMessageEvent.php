<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\MessageBundle\Event;

use Symbb\Core\MessageBundle\Entity\Message;
use Symfony\Component\EventDispatcher\Event;

class ParseMessageEvent extends Event
{

    /**
     * @var Message
     */
    protected $message;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var string
     */
    protected $section;

    public function __construct(Message $message, $text)
    {
        $this->message = $message;
        $this->text = $text;

    }

    /**
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;

    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;

    }

    public function setText($text)
    {
        $this->text = $text;

    }
}
