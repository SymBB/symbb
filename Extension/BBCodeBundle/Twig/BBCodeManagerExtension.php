<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Extension\BBCodeBundle\Twig;

class BBCodeManagerExtension extends \Twig_Extension
{
    /**
     *
     * @var \SymBB\Extension\BBCodeBundle\DependencyInjection\BBCodeManager 
     */
    protected $bbcodeManager;

    public function __construct($bbcodeManager) {
        $this->bbcodeManager   = $bbcodeManager;
    }
    
    public function getFunctions()
    {
        return array(
                new \Twig_SimpleFunction('getSymbbBBCodes', array($this, 'getSymbbBBCodes')),
                new \Twig_SimpleFunction('getSymbbBBCodesJson', array($this, 'getSymbbBBCodesJson'))
        );
    }
    
    public function getSymbbBBCodes($set = 'default'){
        return $this->bbcodeManager->getBBCodes($set);
    }
    
    public function getSymbbBBCodesJson(){
        $codes = $this->bbcodeManager->getBBCodes();
        return json_encode($codes);
    }
    
    public function getName(){
        return 'symbb_post_bbcode_manager';
    }
}