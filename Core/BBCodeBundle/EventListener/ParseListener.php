<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\BBCodeBundle\EventListener;

use SymBB\Core\BBCodeBundle\DependencyInjection\BBCodeManager;
use SymBB\Core\MessageBundle\Event\ParseMessageEvent;

class ParseListener
{

    /**
     *
     * @var \SymBB\Core\BBCodeBundle\DependencyInjection\BBCodeManager
     */
    protected $bbcodeManager;

    /**
     * @param BBCodeManager $bbcodeManager
     */
    public function __construct($bbcodeManager)
    {
        $this->bbcodeManager = $bbcodeManager;

    }

    /**
     * @param \SymBB\Core\ForumBundle\Event\PostManagerParseTextEvent $event
     */
    public function parsePostText(\SymBB\Core\ForumBundle\Event\PostManagerParseTextEvent $event)
    {
        $text = $event->getText();

        $text = $this->bbcodeManager->parse($text, 'post');

        $event->setText($text);

    }

    /**
     * @param \SymBB\Core\ForumBundle\Event\PostManagerParseTextEvent $event
     */
    public function cleanPostText(\SymBB\Core\ForumBundle\Event\PostManagerParseTextEvent $event)
    {
        $text = $event->getText();

        $text = $this->bbcodeManager->clean($text, 'default');

        $event->setText($text);

    }

    /**
     * @param \SymBB\Core\UserBundle\Event\UserParseSignatureEvent $event
     */
    public function parseUserSignature(\SymBB\Core\UserBundle\Event\UserParseSignatureEvent $event)
    {
        $text = $event->getSignature();

        $text = $this->bbcodeManager->parse($text, 'signature');

        $event->setSignature($text);

    }

    /**
     * @param ParseMessageEvent $event
     */
    public function parseMessageText(ParseMessageEvent $event){
        $text = $event->getText();
        $text = $this->bbcodeManager->parse($text, 'pm');
        $event->setText($text);
    }
}