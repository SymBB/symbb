<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\BBCodeBundle\EventListener;

class ParseListener
{

    /**
     *
     * @var \SymBB\Core\BBCodeBundle\DependencyInjection\BBCodeManager
     */
    protected $bbcodeManager;

    public function __construct($bbcodeManager)
    {
        $this->bbcodeManager = $bbcodeManager;

    }

    public function parsePostText(\SymBB\Core\ForumBundle\Event\PostManagerParseTextEvent $event)
    {
        $text = $event->getText();

        $text = $this->bbcodeManager->parse($text, "default");

        $event->setText($text);

    }

    public function cleanPostText(\SymBB\Core\ForumBundle\Event\PostManagerParseTextEvent $event)
    {
        $text = $event->getText();

        $text = $this->bbcodeManager->clean($text, "default");

        $event->setText($text);

    }

    public function parseUserSignature(\SymBB\Core\UserBundle\Event\UserParseSignatureEvent $event)
    {
        $text = $event->getSignature();

        $text = $this->bbcodeManager->parse($text, "signature");

        $event->setSignature($text);

    }
}