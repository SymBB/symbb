<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SiteBundle\Api;

use SymBB\Core\SiteBundle\Entity\Site;
use SymBB\Core\SystemBundle\Api\AbstractApi;

class SiteApi extends AbstractApi
{

    /**
     * @return array
     */
    public function getList(){
        $sites = $this->em->getRepository('SymBBCoreSiteBundle:Site')->findAll();
        return $sites;
    }

    /**
     * @param Site $site
     * @return Site
     */
    public function save(Site $site){
        $this->em->persist($site);
        $this->em->flush();
        return $site;
    }

}