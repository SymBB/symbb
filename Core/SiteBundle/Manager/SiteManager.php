<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SiteBundle\Manager;

use SymBB\Core\SiteBundle\Entity\Navigation\Item;
use SymBB\Core\SiteBundle\Entity\Site;

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

            $sites = $this->em->getRepository('SymBBCoreSiteBundle:Site')->findBy(array());
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
                if (!empty($sites)) {
                    $this->site = reset($sites);
                } else {
                    $this->site = new \SymBB\Core\SiteBundle\Entity\Site();
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

    public function getNavigation(Site $site = null, $naviKey = ''){

        if(!$site){
            $site = $this->getSite();
        }

        if(!empty($naviKey)){
            $navigation = $this->em->getRepository('SymBBCoreSiteBundle:Navigation')->findOneBy(array('site' => $site, 'navKey' => $naviKey));
        } else {
            $navigation = $this->em->getRepository('SymBBCoreSiteBundle:Navigation')->findOneBy(array('site' => $site));
        }

        return $navigation;
    }

    /**
     * @param int $id
     * @return Site
     */
    public function find($id){
        $site = $this->em->getRepository('SymBBCoreSiteBundle:Site')->find($id);
        return $site;
    }

    /**
     * @return mixed
     */
    public function findAll(){
        $sites = $this->em->getRepository('SymBBCoreSiteBundle:Site')->findAll();
        return $sites;
    }

    /**
     * @param Site $site
     * @return bool
     */
    public function save($site){
        $this->em->persist($site);
        $this->em->flush();
        return true;
    }

    /**
     * @param Site $site
     * @return bool
     */
    public function remove($site){
        $this->em->remove($site);
        $this->em->flush();
        return true;
    }
}
