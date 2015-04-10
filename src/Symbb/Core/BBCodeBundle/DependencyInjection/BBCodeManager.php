<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\BBCodeBundle\DependencyInjection;

use Symbb\Core\BBCodeBundle\Entity\Emoticon;
use Symbb\Core\BBCodeBundle\Entity\Set;
use Symbb\Core\BBCodeBundle\Form\Type\BBCode;
use Symbb\Core\SiteBundle\Manager\SiteManager;

class BBCodeManager
{

    protected $em;

    protected $setCache = array();

    protected $allowHtml = true;

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
     * @return \Symbb\Core\BBCodeBundle\Entity\Set
     */
    public function getSet($setId)
    {

        if (!isset($this->setCache[$setId])) {
            $set = $this->em->getRepository('SymbbCoreBBCodeBundle:Set')->find($setId);
            if (!\is_object($set) || $set->getId() <= 0) {
                $set = $this->em->getRepository('SymbbCoreBBCodeBundle:Set')->findOneBy(array());
            }
            $this->setCache[$setId] = $set;
        } else {
            $set = $this->setCache[$setId];
        }

        return $set;
    }

    /**
     *
     * @return \Symbb\Core\BBCodeBundle\Entity\Set[]
     */
    public function getSets()
    {
        $sets = $this->em->getRepository('SymbbCoreBBCodeBundle:Set')->findAll();
        return $sets;
    }

    public function parse($text, $setId = null)
    {
        if (!$this->allowHtml) {
            $text = htmlspecialchars($text, ENT_HTML5, 'UTF-8');
            $text = strip_tags($text);
        }

        $bbcodes = $this->getBBCodes($setId);
        $bbcodes2 = $this->getEmoticons($setId);
        $bbcodes = array_merge($bbcodes, $bbcodes2);

        $this->handleSpecialCasesByRef($text, $bbcodes);

        foreach ($bbcodes as $bbcode) {
            if ($bbcode->getRemoveNewLines()) {
                $regex = $bbcode->getSearchRegex();
                \str_replace('(.+)', '(\s\s+)', $regex);
                //$text = \preg_replace($regex, $bbcode->getReplaceRegex(), $text);
            }
            $text = \preg_replace($bbcode->getSearchRegex(), $bbcode->getReplaceRegex(), $text);
        }

        $text = \nl2br($text);

        // replace urls with html links
        $text = $this->parseUrls($text);

        return $text;
    }

    /**
     * @param $message
     * @return mixed
     */
    public function parseUrls($message){
        $find=array('`((?:https?|ftp)://\S+[[:alnum:]]/?)`si','`((?<!//)(www\.\S+[[:alnum:]]/?))`si');
        $replace=array('<a href="$1" target="_blank">$1</a>','<a href="http://$1"    target="_blank">$1</a>');
        return preg_replace($find,$replace,$message);
    }

    /**
     * @param $text
     * @param BBCode[] $bbcodes
     */
    public function handleSpecialCasesByRef(&$text, $bbcodes)
    {

        foreach ($bbcodes as $bbcode) {
            if ($bbcode->getName() === "Image") {
                $text = preg_replace_callback('#\[img\](.+)\[\/img\]#iUs', function ($matches) {
                    $completeBBCode = $matches[0];
                    $newUrl = $url = $matches[1];
                    if (strpos($newUrl, 'http') !== 0) {
                        $domain = $this->siteManager->getSite()->getMediaDomain();
                        if (substr($domain, 1, -1) === "/") {
                            $domain = rtrim($domain, '/');
                        }
                        $newUrl = $domain . $url;
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
        $bbcodes2 = $this->getEmoticons($setId);
        $bbcodes = array_merge($bbcodes, $bbcodes2);

        foreach ($bbcodes as $bbcode) {
            if ($bbcode->getSearchRegex() != "") {
                $text = \preg_replace($bbcode->getSearchRegex(), '$1', $text);
            }
        }

        $text = \nl2br($text);

        return $text;
    }

    /**
     * @param int $set
     * @return \Symbb\Core\BBCodeBundle\Entity\Emoticon[]
     */
    public function getEmoticons($set = 1)
    {

        $emoticons = array();
        if($set > 0){
            $smilies = array(
                ':D' => "/bundles/symbbtemplatedefault/images/emoticon/default/Big-Grin.png",
                ':)' => "/bundles/symbbtemplatedefault/images/emoticon/default/smile.png",
                ';)' => "/bundles/symbbtemplatedefault/images/emoticon/default/Winking.png",
                ':cool:' => "/bundles/symbbtemplatedefault/images/emoticon/default/Cool.png",
                ':lol:' => "/bundles/symbbtemplatedefault/images/emoticon/default/Laughing.png",
                ':?' => "/bundles/symbbtemplatedefault/images/emoticon/default/Confused.png",
                ':zzz:' => "/bundles/symbbtemplatedefault/images/emoticon/default/Sleeping.png"
            );

            $i = 0;
            foreach ($smilies as $smilie => $image) {
                $bbcodeListItem = new Emoticon();
                $bbcodeListItem->setName($smilie);
                $bbcodeListItem->setSearchRegex('# ' . preg_quote($smilie) . ' #');
                $bbcodeListItem->setReplaceRegex('<img class="smilie" src="' . $image . '" />');
                $bbcodeListItem->setButtonRegex(' ' . $smilie . ' ');
                $bbcodeListItem->setImage($image);
                $bbcodeListItem->setPosition($i++);
                $emoticons[] = $bbcodeListItem;
            }

        }
        return $emoticons;
    }

    /**
     * get a list of grouped BBCodes
     * @param int $setId
     * @return \Symbb\Core\BBCodeBundle\Entity\BBCode[]
     */
    public function getBBCodes($setId = 1)
    {
        $bbcodes = array();
        if($setId > 0){
            $set = $this->getSet($setId);
            $bbcodes = $set->getCodes();
        }
        return $bbcodes;
    }
}