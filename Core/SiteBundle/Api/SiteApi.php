<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SiteBundle\Api;

use SymBB\Core\SystemBundle\Api\AbstractApi;

class SiteApi extends AbstractApi
{

    public function getSites(){
        $sites = $this->em->getRepository('SymBBCoreSiteBundle:Site')->findAll();
        $sites = $this->createArrayOfObject($sites);
        return $sites;
    }

}