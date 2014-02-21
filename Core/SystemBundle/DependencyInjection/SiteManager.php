<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SystemBundle\DependencyInjection;

class SiteManager
{

    /**
     * @var \Doctrine\ORM\EntityManager 
     */
    protected $em;

    /**
     * @var \Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher
     */
    protected $dispatcher;

    /**
     *
     * @var \Symfony\Component\DependencyInjection\Container 
     */
    protected $container;

    protected $site = null;

    /**
     * 
     * @param type $em
     */
    public function __construct($container)
    {
        $this->em = $container->get('doctrine.orm.symbb_entity_manager');
        $this->dispatcher = $container->get('event_dispatcher');
        $this->container = $container;
    }

    public function getSite()
    {
        if ($this->site === null) {
            $host = $this->container->get('request')->getHost();

            $cleanHost = $this->removeUrlPattern($host);


            $sites = $this->em->getRepository('SymBBCoreSystemBundle:Site')->findAll();
            foreach ($sites as $site) {
                $domains = $site->getDomainArray();
                foreach ($domains as $domain) {
                    if ($this->removeUrlPattern($domain) == $cleanHost) {
                        $this->site = $site;
                        return $this->site;
                    }
                }
            }

            if ($this->site === null) {
                $this->site = reset($sites);
                if ($this->site === null) {
                    $this->site = new \SymBB\Core\SystemBundle\Entity\Site();
                }
            }
        }

        return $this->site;
    }

    public function getTemplate($key)
    {
        $site = $this->getSite();
        switch ($key) {
            case 'acp':
                $template = $site->getTemplateAcp();
                break;
            case 'portal':
                $template = $site->getTemplatePortal();
                break;
            case 'email':
                $template = $site->getTemplateEmail();
                break;
            default:
                $template = $site->getTemplateForum();
                break;
        }

        if (empty($template) || $template == "DEFAULT") {
            $template = 'SymBBTemplateDefaultBundle';
        }

        return $template;
    }

    protected function removeUrlPattern($host)
    {
        $host = \str_replace(array('www.', 'http://', 'https://'), '', $host);
        return $host;
    }
}
