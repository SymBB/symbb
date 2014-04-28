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
            $set = $this->em->getRepository('SymBBCoreBBCodeBundle:Set')->findOne();
            $setId = $set->getId();
        }

        $bbcodes = $this->em->getRepository('SymBBCoreBBCodeBundle:BBCode')->findBy(array('set' => $setId));

        foreach ($bbcodes as $bbcode) {
            $text = \preg_replace($bbcode->getSearchRegex(), $bbcode->getReplaceRegex(), $text);
        }
        
        return $text;
    }

    public function clean($text, $setId = null)
    {
        
        
        
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
    public function getBBCodes($set = 1)
    {
        $bbcodes = $this->em->getRepository('SymBBCoreBBCodeBundle:BBCode')->findBy(array('set' => $set));
        return $bbcodes;
    }
}