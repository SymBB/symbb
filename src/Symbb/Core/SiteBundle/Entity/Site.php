<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="sites")
 * @ORM\Entity()
 */
class Site
{

    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $logo;

    /**
     * @ORM\Column(type="text")
     */
    protected $metaDataDescription = "";

    /**
     * @ORM\Column(type="text")
     */
    protected $metaDataKeywords = "";

    /**
     * @ORM\Column(type="text")
     */
    protected $domains = "";

    /**
     * @ORM\Column(type="string")
     */
    protected $mediaDomain = "";

    /**
     * @ORM\Column(type="string")
     */
    protected $templateAcp = 'DEFAULT';

    /**
     * @ORM\Column(type="string", unique=false)
     */
    protected $templateForum = 'DEFAULT';

    /**
     * @ORM\Column(type="string", unique=false)
     */
    protected $templateEmail = 'DEFAULT';

    /**
     * @ORM\Column(type="string", unique=false)
     */
    protected $templatePortal = 'DEFAULT';

    /**
     * @ORM\Column(type="text")
     */
    protected $announcement = '';

    /**
     * @ORM\OneToMany(targetEntity="Symbb\Core\SiteBundle\Entity\Navigation", mappedBy="site")
     * @ORM\OrderBy()
     */
    protected $navigations;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $googleTrackingId;

    /**
     * @ORM\Column(type="text")
     */
    protected $email = "";

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getMetaDataDescription()
    {
        return $this->metaDataDescription;
    }

    public function setMetaDataDescription($value)
    {
        $this->metaDataDescription = $value;
    }

    public function getMetaDataKeywords()
    {
        return $this->metaDataKeywords;
    }

    public function setMetaDataKeywords($value)
    {
        $this->metaDataKeywords = $value;
    }

    public function getTemplateAcp()
    {
        return $this->templateAcp;
    }

    public function setTemplateAcp($template)
    {
        $this->templateAcp = $template;
    }

    public function setTemplateForum($template)
    {
        $this->templateForum = $template;
    }

    public function getTemplateForum()
    {
        return $this->templateForum;
    }

    public function setTemplateEmail($template)
    {
        $this->templateEmail = $template;
    }

    public function getTemplateEmail()
    {
        return $this->templateEmail;
    }

    public function getTemplatePortal()
    {
        return $this->templatePortal;
    }

    public function setTemplatePortal($template)
    {
        $this->templatePortal = $template;
    }

    public function getDomains()
    {
        return $this->domains;
    }

    public function getDomainArray()
    {
        $domans = $this->domains;
        $domans = \explode(',', $domans);
        return $domans;
    }

    public function setDomains($domains)
    {
        $this->domains = $domains;
    }

    public function getMediaDomain()
    {
        $domain = $this->mediaDomain;
        if (empty($domain)) {
            $domains = $this->getDomainArray();
            $domain = reset($domains);
        }
        if (strpos($domain, 'http') !== 0 && strpos($domain, 'ftp') !== 0) {
            $domain = 'http://' . $domain;
        }
        return $domain;
    }

    public function setMediaDomain($domain)
    {
        if (strpos($domain, 'http') !== 0 && strpos($domain, 'ftp') !== 0 && !empty($domain)) {
            $domain = 'http://' . $domain;
        } else if ($domain === 'http://') {
            $domain = '';
        }
        $this->mediaDomain = $domain;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @param mixed $navigations
     */
    public function setNavigations($navigations)
    {
        $this->navigations = $navigations;
    }

    /**
     * @return mixed
     */
    public function getNavigations()
    {
        return $this->navigations;
    }

    /**
     * @param string $announcement
     */
    public function setAnnouncement($announcement)
    {
        $this->announcement = $announcement;
    }

    /**
     * @return string
     */
    public function getAnnouncement()
    {
        return $this->announcement;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param mixed $logo
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    /**
     * @return mixed
     */
    public function getGoogleTrackingId()
    {
        return $this->googleTrackingId;
    }

    /**
     * @param mixed $googleTrackingId
     */
    public function setGoogleTrackingId($googleTrackingId)
    {
        $this->googleTrackingId = $googleTrackingId;
    }
}