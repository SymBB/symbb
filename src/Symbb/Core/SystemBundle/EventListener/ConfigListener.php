<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SystemBundle\EventListener;

class ConfigListener
{

    public function configs(\Symbb\Core\SystemBundle\Event\ConfigDefaultsEvent $event)
    {

        $terms = "By accessing “symBB.de” (hereinafter “we”, “us”, “our”, “symBB.de”, “http://www.symBB.de”), you agree to be legally bound by the following terms. 
If you do not agree to be legally bound by all of the following terms then please do not access and/or use “symBB.de”. We may change these at any time and we’ll do our utmost in informing you, though it would be prudent to review this regularly yourself as your continued usage of “symBB.de” after changes mean you agree to be legally bound by these terms as they are updated and/or amended.
Our forums are powered by symBB (hereinafter “they”, “them”, “their”, “symBB software”, “www.symBB.de”, “Christian Wielath”, “symBB Teams”) which is a bulletin board solution released under the “General Public License” (hereinafter “GPL”) and can be downloaded from www.symBB.de. The symBB software only facilitates internet based discussions, the symBB Group are not responsible for what we allow and/or disallow as permissible content and/or conduct. For further information about symBB, please see: http://www.symBB.de/.
You agree not to post any abusive, obscene, vulgar, slanderous, hateful, threatening, sexually-orientated or any other material that may violate any laws be it of your country, the country where “symBB.de” is hosted or International Law. Doing so may lead to you being immediately and permanently banned, with notification of your Internet Service Provider if deemed required by us. The IP address of all posts are recorded to aid in enforcing these conditions. You agree that “symBB.de” have the right to remove, edit, move, close or put on moderation queue any topic at any time should we see fit based on the site wide rules as well as forum specific rules (published within the specific forums).
As a user you agree to any information you have entered to being stored in a database. While this information will not be disclosed to any third party without your consent, neither “symBB.de” nor symBB Team shall be held responsible for any hacking attempt that may lead to the data being compromised.";

        $event->setDefaultConfig('system.name', 'Symbb Test System', "text", $this->getSection());
        $event->setDefaultConfig('system.email', 'your@email.com', "email", $this->getSection());
        $event->setDefaultConfig('system.imprint', '', "bbcode", $this->getSection());
        $event->setDefaultConfig('system.terms', $terms, "bbcode", $this->getSection());
        $event->setDefaultConfig('system.registration.enabled', true, "checkbox", $this->getSection());
        
    }

    protected function getSection()
    {
        return "default";
    }
}