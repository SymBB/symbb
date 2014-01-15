<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Extension\BBCodeBundle\DependencyInjection;

class BBCodeManager {
    
    protected $decodaManager;
    protected $translator;
    protected $config;


    public function __construct($container) {
        $this->decodaManager    = $container->get('fm_bbcode.decoda_manager');
        $this->translator       = $container->get('translator');
        $this->config           = $container->getParameter('symbb_extension_bbcode');
    }
    
    public function getEmoticons(){
    }
    
    /**
     * get a list of grouped BBCodes
     * @return array
     */
    public function getBBCodes($set = 'default'){
        $bbcodes = array();
        
        foreach($this->config['bbcodes'][$set] as $group => $bbcodeGroups){
            foreach($bbcodeGroups as $tag => $bbcode){
                $bbcodes[$group][$tag] = $bbcode;
                $bbcodes[$group][$tag]['tag'] = $tag;
                if(!isset($bbcodes[$group][$tag]['image']) || !$bbcodes[$group][$tag]['image']){
                    $bbcodes[$group][$tag]['image'] = '/bundles/symbbextensionbbcode/images/default.png';
                }
            }
        }
        
        return $bbcodes;
    }
}