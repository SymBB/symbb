<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Extension\UserTagBundle\EventListener;

class ParseListener
{

    /**
     *
     * @var \SymBB\Core\UserBundle\DependencyInjection\UserManager 
     */
    protected $userManager;

    public function __construct($userManager)
    {
        $this->userManager = $userManager;

    }

    public function parsePostText(\SymBB\Core\ForumBundle\Event\PostManagerParseTextEvent $event)
    {
        $text = $event->getText();

        $matches = array();
        
        preg_match_all(
            "|\W@(\S+)?|", $text, $matches);

        if(isset($matches[1])){
            foreach($matches[1] as $username){
                $userFound = $this->userManager->findByUsername($username);
                if(\is_object($userFound)){
                    $text = \str_replace("@".$username, "<a>@".$userFound->getUsername()."</a>", $text);
                }
            }
        }

        
        $event->setText($text);

    }
}