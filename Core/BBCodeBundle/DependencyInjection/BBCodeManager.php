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
        
        if (!$setId) {
            $set = $this->em->getRepository('SymBBCoreBBCode:Set')->findOne();
            $setId = $set->getId();
        }

        $bbcodes = $this->em->getRepository('SymBBCoreBBCode:BBCode')->findBy(array('set' => $setId));

        foreach ($bbcodes as $bbcode) {
            $text = \preg_replace($bbcode->getReplaceRegex(), $bbcode->getReplaceReqex(), $text);
        }
        
        return $text;
    }

    public function clean($text)
    {
        return $text;
    }

    public function getEmoticons($set = "defauult")
    {
        return array();
    }

    /**
     * get a list of grouped BBCodes
     * @return array
     */
    public function getBBCodes($set = 'default')
    {
        $bbcodes = array();

        foreach ($this->config['bbcodes'][$set] as $group => $bbcodeGroups) {
            foreach ($bbcodeGroups as $tag => $bbcode) {
                $bbcodes[$group][$tag] = $bbcode;
                $bbcodes[$group][$tag]['tag'] = $tag;
                if (!isset($bbcodes[$group][$tag]['image']) || !$bbcodes[$group][$tag]['image']) {
                    $bbcodes[$group][$tag]['image'] = '/bundles/symbbextensionbbcode/images/default.png';
                }
            }
        }

        return $bbcodes;
    }
}