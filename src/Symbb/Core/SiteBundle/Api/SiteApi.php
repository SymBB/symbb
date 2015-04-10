<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SiteBundle\Api;

use Symbb\Core\SiteBundle\Entity\Navigation;
use Symbb\Core\SiteBundle\Entity\Navigation\Item;
use Symbb\Core\SiteBundle\Entity\Site;
use Symbb\Core\SiteBundle\Manager\SiteManager;
use Symbb\Core\SystemBundle\Api\AbstractApi;

class SiteApi extends AbstractApi
{
    const ERROR_WRONG_OBJECT = 'you have passed a wrong object';

    /**
     * @var SiteManager
     */
    protected $siteManager;

    public function find($id)
    {
        $site = $this->siteManager->find($id);
        if (!is_object($site)) {
            $this->addErrorMessage(self::ERROR_ENTRY_NOT_FOUND);
        }
        return $site;
    }

    /**
     * return a array with all Sites
     * @return array
     */
    public function getList()
    {
        $sites = $this->siteManager->findAll();
        $this->addPaginationData($sites);
        $sites = $sites->getItems();
        if (empty($sites)) {
            $this->addInfoMessage(self::INFO_NO_ENTRIES_FOUND);
        }
        return $sites;
    }

    /**
     * save a Site
     * you can pass the Site object or an array with the fields
     * if you pass an array the keys must be with underscore and not with CamelCase
     * @param Site|array $site
     * @return Site
     */
    public function save($site)
    {

        if (is_array($site)) {
            $siteData = $site;
            if ($site['id'] > 0) {
                $site = $this->find($site['id']);
            } else {
                $site = new Site();
            }
            $this->assignArrayToObject($site, $siteData);
        } else if (!($site instanceof Site)) {
            $this->addErrorMessage(self::ERROR_WRONG_OBJECT);
        }

        if (!$this->hasError()) {
            $check = $this->siteManager->save($site);
            if ($check) {
                $this->addSuccessMessage(self::SUCCESS_SAVED);
            }
        }

        return $site;
    }

    /**
     * @param int|Site $site
     * @return bool
     */
    public function delete($site)
    {
        if (is_numeric($site)) {
            $site = $this->find($site);
        } else if (!($site instanceof Site)) {
            $this->addErrorMessage(self::ERROR_WRONG_OBJECT);
        }
        if (!$this->hasError()) {
            $check = $this->siteManager->remove($site);
            if ($check) {
                $this->addSuccessMessage(self::SUCCESS_DELETED);
            }
            return $check;
        }
        return false;
    }

    /**
     * @param $object
     * @param $direction
     * @return array|null
     */
    protected function getFieldsForObject($object, $direction)
    {

        //todo get it form navi api
        $fields = array();
        if ($object instanceof Item) {
            // only this fields are allowed
            $fields = array(
                'id',
                'title',
                'type',
                'symfony_route',
                'symfony_route_params',
                'fix_url',
                'position'
            );
            if ($direction == "toArray") {
                $fields[] = 'children';
            }
        } else if ($object instanceof Navigation) {
            $fields = array(
                'id',
                'title',
                'nav_key'
            );
            if ($direction == "toArray") {
                $fields[] = 'items';
            }
        } else if ($object instanceof Site) {
            // only this fields are allowed
            $fields = array(
                'id',
                'announcement',
                'domains',
                'media_domain',
                'meta_data_description',
                'meta_data_keywords',
                'name',
                'email',
                'template_acp',
                'template_email',
                'template_forum',
                'template_portal',
                'google_tracking_id',
                'logo'
            );
            if ($direction == "toArray") {
                $fields[] = 'navigations';
            }
        }

        return $fields;
    }

    public function setSiteManager(SiteManager $siteManager)
    {
        $this->siteManager = $siteManager;
    }
}