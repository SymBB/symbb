<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Extension\BBCodeBundle\EventListener;

class ParseListener
{

    protected $templatingHelper;

    public function __construct($templatingHelper)
    {
        $this->templatingHelper = $templatingHelper;

    }

    public function parsePostText(\SymBB\Core\ForumBundle\Event\PostManagerParseTextEvent $event)
    {
        $text = $event->getText();

        $text = $this->templatingHelper->filter($text, "default");

        $event->setText($text);

    }

    public function cleanPostText(\SymBB\Core\ForumBundle\Event\PostManagerParseTextEvent $event)
    {
        $text = $event->getText();

        $text = $this->templatingHelper->clean($text, "default");

        $event->setText($text);

    }

    public function parseUserSignature(\SymBB\Core\UserBundle\Event\UserParseSignatureEvent $event)
    {
        $text = $event->getSignature();

        $text = $this->templatingHelper->filter($text, "signature");

        $event->setSignature($text);

    }
}