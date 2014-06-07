<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\BBCodeBundle\DependencyInjection;

use SymBB\Core\BBCodeBundle\Form\Type\BBCode;
use SymBB\Core\SiteBundle\DependencyInjection\SiteManager;

class BBCodeManager
{

    protected $em;

    protected $setCache = array();

    /**
     * @var SiteManager
     */
    protected $siteManager;

    public function __construct($em, SiteManager $siteManager)
    {
        $this->em = $em;
        $this->siteManager = $siteManager;
    }

    /**
     * 
     * @param string $setId
     * @return \SymBB\Core\BBCodeBundle\Entity\Set
     */
    public function getSet($setId)
    {

        if (!isset($this->setCache[$setId])) {
            $set = $this->em->getRepository('SymBBCoreBBCodeBundle:Set')->find($setId);
            if (!\is_object($set) || $set->getId() <= 0) {
                $set = $this->em->getRepository('SymBBCoreBBCodeBundle:Set')->findOneBy(array());
            }
            $this->setCache[$setId] = $set;
        } else {
            $set = $this->setCache[$setId];
        }

        return $set;
    }

    /**
     * 
     * @return \SymBB\Core\BBCodeBundle\Entity\Set[]
     */
    public function getSets()
    {
        $sets = $this->em->getRepository('SymBBCoreBBCodeBundle:Set')->findAll();
        return $sets;
    }

    public function parse($text, $setId = null)
    {
        $text = htmlspecialchars($text, ENT_HTML5, 'UTF-8');
        $text = strip_tags($text);

        $bbcodes = $this->getBBCodes($setId);

        $this->handleSpecialCasesByRef($text, $bbcodes);

        foreach ($bbcodes as $bbcode) {
            if ($bbcode->getRemoveNewLines()) {
                $regex = $bbcode->getSearchRegex();
                $regex = \str_replace('(.+)', '(\s\s+)', $regex);
                //$text = \preg_replace($regex, $bbcode->getReplaceRegex(), $text);
            }
            $text = \preg_replace($bbcode->getSearchRegex(), $bbcode->getReplaceRegex(), $text);
        }

        $text = \nl2br($text);
        return $text;
    }

    /**
     * @param $text
     * @param BBCode[] $bbcodes
     */
    public function handleSpecialCasesByRef(&$text, $bbcodes){

        foreach($bbcodes as $bbcode){
            if($bbcode->getName() === "Image"){
                $text = preg_replace_callback('#\[img\](.+)\[\/img\]#iUs', function($matches){
                    $completeBBCode = $matches[0];
                    $newUrl = $url = $matches[1];
                    if(strpos($newUrl, 'http') !== 0){
                        $domain = $this->siteManager->getSite()->getMediaDomain();
                        if(substr($domain, 1 , -1) === "/"){
                            $domain = rtrim($domain, '/');
                        }
                        $newUrl = $domain.$url;
                    }
                    return str_replace($url, $newUrl, $completeBBCode);
                }, $text);
            }
        }


    }

    public function clean($text, $setId = null)
    {

        $text = htmlspecialchars($text, ENT_HTML5, 'UTF-8');
        $text = strip_tags($text);

        $bbcodes = $this->getBBCodes($setId);

        foreach ($bbcodes as $bbcode) {
            if ($bbcode->getSearchRegex() != "") {
                $text = \preg_replace($bbcode->getSearchRegex(), '$1', $text);
            }
        }

        $text = \nl2br($text);

        return $text;
    }

    public function getEmoticons($set = 1)
    {
        return array();
    }

    /**
     * get a list of grouped BBCodes
     * @return \SymBB\Core\BBCodeBundle\Entity\BBCode
     */
    public function getBBCodes($setId = 1)
    {
        $set = $this->getSet($setId);
        $bbcodes = $set->getCodes();
        return $bbcodes;
    }
}