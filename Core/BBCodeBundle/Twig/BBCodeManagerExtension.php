<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\BBCodeBundle\Twig;

class BBCodeManagerExtension extends \Twig_Extension
{

    /**
     *
     * @var \SymBB\Core\BBCodeBundle\DependencyInjection\BBCodeManager 
     */
    protected $bbcodeManager;

    protected $serializer;

    public function __construct($bbcodeManager, $serializer)
    {
        $this->bbcodeManager = $bbcodeManager;
        $this->serializer = $serializer;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getSymbbBBCodeManager', array($this, 'getSymbbBBCodeManager')),
            new \Twig_SimpleFunction('cleanSymBBBBCodes', array($this, 'cleanSymBBBBCodes')),
            new \Twig_SimpleFunction('parseSymbBBBCodes', array($this, 'parseSymbBBBCodes')),
            new \Twig_SimpleFunction('getSymBBBBCodes', array($this, 'getSymBBBBCodes')),
            new \Twig_SimpleFunction('getSymbbBBCodeDataForJs', array($this, 'getSymbbBBCodeDataForJs'))
        );
    }

    public function getSymbbBBCodeManager()
    {
        return $this->bbcodeManager;
    }

    public function cleanSymBBBBCodes($text, $setId = 1)
    {
        return $this->bbcodeManager->clean($text, $setId);
    }

    public function parseSymbBBBCodes($text, $setId = 1)
    {
        return $this->bbcodeManager->parse($text, $setId);
    }

    public function getSymBBBBCodes($setId = 1)
    {
        return $this->bbcodeManager->getBBCodes($setId);
    }

    public function getSymbbBBCodeDataForJs()
    {
        $data = array();
        $sets = $this->bbcodeManager->getSets();
        foreach ($sets as $set) {
            $data[$set->getId()] = array();
            foreach ($set->getCodes() as $code) {
                $data[$set->getId()][] = $this->serializer->serialize($code, 'json');
            }
        }
        return $data;
    }

    public function getName()
    {
        return 'symbb_core_bbcode_twig';
    }
}