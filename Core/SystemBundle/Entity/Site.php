<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SystemBundle\Entity;

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
     * @ORM\Column(type="string", unique=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="text", unique=false)
     */
    protected $domains;

    /**
     * @ORM\Column(type="string", unique=false)
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
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $position = 999;

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
        $domans =  $this->domains;
        $domans = \explode(',', $domans);
        return $domans;
    }

    public function setDomains($domains)
    {
        $this->domains = $domains;
    }
}