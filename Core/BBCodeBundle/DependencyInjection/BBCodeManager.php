<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\BBCodeBundle\DependencyInjection;

class BBCodeManager
{

    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function parse($text, $setId = null)
    {
        $text = strip_tags($text);

        $bbcodes = $this->getBBCodes($setId);

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

    public function clean($text, $setId = null)
    {
        
        $text = strip_tags($text);

        $bbcodes = $this->getBBCodes($setId);

        foreach ($bbcodes as $bbcode) {
            if ($bbcode->getCleanRegex() != "") {
                $text = \preg_replace($bbcode->getCleanRegex(), '$1', $text);
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

        if (!$setId) {
            $set = $this->em->getRepository('SymBBCoreBBCodeBundle:Set')->findOne();
            $setId = $set->getId();
        } else {
            $set = $this->em->getRepository('SymBBCoreBBCodeBundle:Set')->find($setId);
        }

        $bbcodes = $set->getCodes();
        return $bbcodes;
    }
}