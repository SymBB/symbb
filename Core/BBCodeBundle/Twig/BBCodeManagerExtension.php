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
     * @var \SymBB\Extension\BBCodeBundle\DependencyInjection\BBCodeManager 
     */
    protected $bbcodeManager;

    public function __construct($bbcodeManager)
    {
        $this->bbcodeManager = $bbcodeManager;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getSymbbBBCodeManager', array($this, 'getSymbbBBCodeManager')),
            new \Twig_SimpleFunction('cleanSymbbBBCodes', array($this, 'cleanSymbbBBCodes')),
            new \Twig_SimpleFunction('parseSymbbBBCodes', array($this, 'parseSymbbBBCodes')),
            new \Twig_SimpleFunction('getSymbbBBCodes', array($this, 'getSymbbBBCodes'))
        );
    }

    public function getSymbbBBCodeManager()
    {
        return $this->bbcodeManager;
    }

    public function cleanSymbbBBCodes($text)
    {
        return $this->bbcodeManager->clean($text);
    }

    public function parseSymbbBBCodes($text)
    {
        return $this->bbcodeManager->parse($text);
    }

    public function getSymbbBBCodes()
    {
        return $this->bbcodeManager->getBBCodes();
    }


    public function getName()
    {
        return 'symbb_core_bbcode_twig';
    }
}