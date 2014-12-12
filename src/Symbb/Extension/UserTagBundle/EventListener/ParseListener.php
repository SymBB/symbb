<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\UserTagBundle\EventListener;

class ParseListener
{

    /**
     *
     * @var \Symbb\Core\UserBundle\Manager\UserManager
     */
    protected $userManager;

    protected $router;
    
    public function __construct($userManager, $router)
    {
        $this->userManager = $userManager;
        $this->router = $router;
    }

    public function parsePostText(\Symbb\Core\ForumBundle\Event\PostManagerParseTextEvent $event)
    {
        $text = $event->getText();

        $matches = array();

        preg_match_all(
            "|\W@(\S+)?|", $text, $matches);

        if (isset($matches[1])) {
            foreach ($matches[1] as $username) {
                $userFound = $this->userManager->findByUsername($username);
                if (\is_object($userFound)) {
                    $uri = $this->router->generate('symbb_user_profile', array('id' => $userFound->getId(), 'name' => $userFound->getUsername()));
                    $text = \str_replace("@" . $username, "<a href='".$uri."'>@" . $userFound->getUsername() . "</a>", $text);
                }
            }
        }

        $event->setText($text);

    }
}