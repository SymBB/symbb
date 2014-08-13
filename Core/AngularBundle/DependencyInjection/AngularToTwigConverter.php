<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\AngularBundle\DependencyInjection;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Class AngularToTwigConverter
 * @package SymBB\Core\AngularBundle\DependencyInjection
 */
class AngularToTwigConverter
{

    /**
     * @var string
     */
    protected $html = '';
    /**
     * @var string
     */
    protected $parentTemplate = '';

    protected static $macroData = array();

    protected $router;

    public static $tempData = '';

    public function __construct($router){
        $this->router = $router;
    }

    /**
     * @param $html
     */
    public function setHtml($html){
        $this->html = $html;
    }

    /**
     * @param $parentTemplate
     */
    public function setParentTemplate($parentTemplate){
        $this->parentTemplate = $parentTemplate;

    }

    /**
     * @return string
     */
    public function convert(){


        $this->html = str_replace('$parent', '_context', $this->html);
        $this->html = str_replace('$last', '0', $this->html); //loop.last not working with array?
        $this->html = str_replace('$first', 'loop.first', $this->html);
        $this->html = str_replace('$index', 'loop.index', $this->html);
        $this->html = str_replace('ng-href', 'href', $this->html);


        // replace angular syntax to twig syntax
        $this->html = preg_replace_callback('#\[\[(.*)\]\]#iU', function($matches){
                return '{{ '.$matches[1].' }}';
        }, $this->html);

        // replace angular date filter to twig date filter
        $this->html = preg_replace_callback('#date\:\'(.+)\'#iU', function($matches){
            return 'date("d.m.Y H:i:s")';
        }, $this->html);

        // change angular init to twig asign
        $this->html = preg_replace_callback('#(<.+ ng-init="(.+)=(.+)".*>)#iU', function($matches){
            return $matches[0].' {% set '.$matches[2].' = '.$matches[3].' %}';
        }, $this->html);

        $this->html = preg_replace_callback('#<script type="text/ng-template".*id="(.+)".*>(.*)</script>#iUs', function($matches){
            $tmp = substr($matches[0],8);
            $tmp = substr($tmp, 0, -7);
            $tmp = '<twigmacro '.$tmp.'twigmacro>';
            return $tmp;
        }, $this->html);


        //**** CRAWLER *****//
        $crawler = new Crawler( $this->html, 'dummyUrl');

        // replace angular include
        $crawlerRepeat = $crawler->filter('[symbb-bot-hide] ');
        foreach($crawlerRepeat as $node){
            /**
             * @var \DOMElement $node
             */
            $node->parentNode->removeChild($node);
        }

        // replace angular include
        $crawlerRepeat = $crawler->filter('[ng-include] ');
        foreach($crawlerRepeat as $repeatElement){
            /**
             * @var \DOMElement $repeatElement
             */
            AngularToTwigConverter::convertNgIncludeToTwig($repeatElement);
        }

        // change angular repeat to twig loop
        $crawlerRepeat = $crawler->filter('[ng-repeat] ');
        foreach($crawlerRepeat as $repeatElement){
            /**
             * @var \DOMElement $repeatElement
             */
            AngularToTwigConverter::convertNgRepeatToTwig($repeatElement);
        }
        $this->html = $crawler->html();

        // change angular if to twig if
        $crawlerRepeat = $crawler->filter('[ng-if] ');
        foreach($crawlerRepeat as $repeatElement){
            /**
             * @var \DOMElement $repeatElement
             */
            AngularToTwigConverter::convertNgIfToTwig($repeatElement);
        }
        $crawlerRepeat = $crawler->filter('[ng-show] ');
        foreach($crawlerRepeat as $repeatElement){
            /**
             * @var \DOMElement $repeatElement
             */
            AngularToTwigConverter::convertNgIfToTwig($repeatElement);
        }


        $crawlerRepeat = $crawler->filter('[symbb-js-link]');
        foreach($crawlerRepeat as $repeatElement){
            /**
             * @var \DOMElement $repeatElement
             */
            AngularToTwigConverter::convertSymbbLinkToTwig($repeatElement, $this->router);
        }
        $crawlerRepeat = $crawler->filter('[symbb-link]');
        foreach($crawlerRepeat as $repeatElement){
            /**
             * @var \DOMElement $repeatElement
             */
            AngularToTwigConverter::convertSymbbLinkToTwig($repeatElement, $this->router);
        }

        // remove request because "items" can be a array -> twig error and additional we dont need this for search bots
        $crawlerRepeat = $crawler->filter('[symbb-request]');
        foreach($crawlerRepeat as $repeatElement){
            $repeatElement->parentNode->removeChild($repeatElement);
        }

        $crawlerRepeat = $crawler->filter('[symbb-sf-link]');
        foreach($crawlerRepeat as $repeatElement){
            /**
             * @var \DOMElement $repeatElement
             */
            AngularToTwigConverter::convertSymbbLinkToTwig($repeatElement, $this->router);
        }

        $crawlerRepeat = $crawler->filter('[ng-bind-html-unsafe]');
        foreach($crawlerRepeat as $repeatElement){
            /**
             * @var \DOMElement $repeatElement
             */
            AngularToTwigConverter::convertNgBindHtmlUnsave($repeatElement, $this->router);
        }

        $crawlerRepeat = $crawler->filter('[ng-bind-html]');
        foreach($crawlerRepeat as $repeatElement){
            /**
             * @var \DOMElement $repeatElement
             */
            AngularToTwigConverter::convertNgBindHtml($repeatElement, $this->router);
        }

        //Crawler HTML
        $this->html = $crawler->html();
        //**** CRAWLER END *****//

        // change angular templates to macros
        $this->html = preg_replace_callback('#<twigmacro type="text/ng-template".*id="(.+)".*>(.*)</twigmacro>#iUs', function($matches){
            $key = 'symbb_'.md5("'".$matches[1]."'");
            AngularToTwigConverter::$macroData[$key]['macro'] = ' {% macro '.$key.'() %} '.$matches[2].' {% endmacro %} ';
            return '';
        }, $this->html);


        if($this->parentTemplate !== ""){
            $this->html = "{% extends getSymbbTemplate('forum') ~ '".$this->parentTemplate."' %} {% block symbb_body %} ".$this->html.' {% endblock %}';
        }

        //add now all macros!
        foreach(AngularToTwigConverter::$macroData as $key => $macro){
            $macroHtml = $macro['macro'];
            if(isset($macro['parameters'])){
                $macroHtml = str_replace($key.'()', $key.'('.$macro['parameters'].')', $macroHtml);
            }
            $this->html = $macroHtml .' ' . $this->html;
        }

        // crawler will return twig syntax with html encode
        $this->html = urldecode($this->html);

        return $this->html;
    }

    protected static function convertNgIncludeToTwig(\DOMElement $node){
        $includeKey = "";
        $repeatData = "";
        foreach ($node->attributes as $attrName => $attrNode) {
            /**
             * @var \DOMAttr $attrNode
             */
            if($attrName == "ng-include"){
                $includeKey = $attrNode->value;
            }
            if($attrName == "ng-repeat"){
                $repeatData = $attrNode->value;
            }

        }
        $additionalParameters = '_context, user';
        if(!empty($repeatData)){
            $repeatData = explode('in', $repeatData);
            $repeatData = reset($repeatData);
            $repeatData = trim($repeatData);
            $additionalParameters .= ', '.$repeatData;
        }
        $key = 'symbb_'.md5($includeKey);
        $data = $node->ownerDocument->createTextNode("{{ _self.".$key."($additionalParameters) }}");
        AngularToTwigConverter::$macroData[$key]['parameters'] = $additionalParameters;
        $node->appendChild($data);
    }

    protected static function convertNgIfToTwig(\DOMElement $node){
        $ifData = "";
        foreach ($node->attributes as $attrName => $attrNode) {
            /**
             * @var \DOMAttr $attrNode
             */
            if($attrName == "ng-if"){
                $ifData = $attrNode->value;
                break;
            } else if($attrName == "ng-show"){
                $ifData = $attrNode->value;
                break;
            }

        }

        $parts = array();
        $temp = explode(' && ', $ifData);
        foreach($temp as $tmp){
            $temp2 = explode(' || ', $tmp);
            foreach($temp2 as $tmp2){
               if(count($temp2) > 1){
                   $parts[] = array('value' => $tmp2, 'condition' => 'or');
               } else {
                   $parts[] = array('value' => $tmp2, 'condition' => 'and');
               }
            }
        }

        $ifDataNew = '';
        foreach($parts as $k => $part){
            $conditionPos = strpos($part['value'], ' ');
            $conditionPart = '';
            if($conditionPos){
                $conditionPart = substr($part['value'], $conditionPos);
                $part['value'] = substr($part['value'], 0, $conditionPos);
            }
            $parts2 = explode('.', $part['value']);
            $last = '';
            $ifDataNew .= ' ( ';
            $not = false;
            foreach($parts2 as $key => $tmp){
                if(strpos($tmp, '!') === 0){
                    $not = true;
                }
                if($key > 0){
                    $last .= '.';
                }
                $last .= $tmp;

                $ifDataNew .= $last.' is defined ';

                if($not){
                    $ifDataNew .= ' or ';
                } else {
                    $ifDataNew .= ' and ';
                }
            }
            $ifDataNew .= $part['value'].$conditionPart;
            $ifDataNew .= ' ) ';
            if($k < (count($parts) - 1)){
                $ifDataNew .= $part['condition'];
            }
        }

        $ifData = $ifDataNew;

        $ifData = str_replace("!=", 'is not', $ifData);
        $ifData = str_replace("!", ' not ', $ifData);
        $ifData = str_replace("&&", 'and', $ifData);
        $ifData = str_replace("is not", '!=', $ifData);
        $start = $node->ownerDocument->createTextNode("{% if ".$ifData." %}");
        $end = $node->ownerDocument->createTextNode("{% endif %}");
        $nextNode = $node->nextSibling;
        $node->parentNode->insertBefore($start, $node);
        $node->parentNode->insertBefore($end, $nextNode);
        return $node;
    }

    protected static function convertSymbbLinkToTwig(\DOMElement $node, $router){
        $childnodes = array();
        foreach ($node->childNodes as $child){
            $childnodes[] = $child;
        }

        $linkData = "";
        $params = array();
        $newElement = false;
        $angularLink = true;
        foreach ($node->attributes as $attrName => $attrNode) {
            /**
             * @var \DOMAttr $attrNode
             */
            if($attrName == "symbb-js-link"){
                $linkData = $attrNode->value;
                $newElement = true;
            } else if($attrName == "symbb-link"){
                $linkData = $attrNode->value;
            }  else if($attrName == "symbb-sf-link"){
                $linkData = $attrNode->value;
                $angularLink = false;
            } else if(strpos($attrName, 'param-') === 0){
                $key = substr($attrName, 6);
                $value = $attrNode->value;
                $value = str_replace(array('{', '}'), '', $value);
                $value = trim($value);
                if($value == 'last'){
                    $value = "'last'";
                }
                $params[$key] = trim($value);
            }

        }

        $paramsJsonArray = array();
        foreach($params as $paramKey => $paramValue){
            if(strpos($paramKey, '-')){
                $paramKey2 = explode('-', $paramKey);
                $paramKeyNew = '';
                foreach($paramKey2 as $k => $key){
                    if($k !== 0){
                        $paramKeyNew .= ucfirst($key);
                    } else {
                        $paramKeyNew .= $key;
                    }
                }
                unset($params[$paramKey]);
                $params[$paramKeyNew] = $paramValue;
                $paramKey = $paramKeyNew;
            }
            $paramsJsonArray[] = "'".$paramKey."':".$paramValue;
        }

        $paramsJson = '{'.implode(', ', $paramsJsonArray).'}';

        if($angularLink){
            $linkData = 'angular_locale_'.$linkData;
        }

        $path = "{{ path('".$linkData."', ".$paramsJson.") }}";
        $newnode = $node->ownerDocument->createElement('a');
        $newnode->setAttribute('href', $path);

        if($newElement){
            foreach ($childnodes as $child){
                $node->removeChild($child);
                $child2 = $node->ownerDocument->importNode($child, true);
                $newnode->appendChild($child2);
            }
            $node->appendChild($newnode);
        } else {
            $node->parentNode->replaceChild($newnode, $node);
            foreach ($childnodes as $child){
                $child2 = $node->ownerDocument->importNode($child, true);
                $newnode->appendChild($child2);
            }
        }

        return $newnode;
    }

    protected  static function convertNgRepeatToTwig(\DOMElement $node) {
        $loopVariables = "";
        foreach ($node->attributes as $attrName => $attrNode) {
            /**
             * @var \DOMAttr $attrNode
             */
            if($attrName == "ng-repeat"){
                $loopVariables = $attrNode->value;
                break;
            }

        }

        $loopVariables = explode('| filter', $loopVariables);
        $filters = '';
        if(count($loopVariables) > 1){
            $filters =  end($loopVariables);
        }
        $loopVariables = reset($loopVariables);
        $loopVariables = trim($loopVariables);

        AngularToTwigConverter::$tempData = $loopVariables;

        $newFilter = '';

        if(!empty($filters)){
            $newFilter = preg_replace_callback('#.*\{(.+)\}#iU', function($filters){

                $temp = explode('in', AngularToTwigConverter::$tempData);
                $objectName = reset($temp);
                $objectName = trim($objectName);

                $temp = array();
                $filters = $filters[1];
                $filters  = explode(',', $filters);
                foreach($filters as $filter){
                    $filter = trim($filter);
                    $filter = explode(':', $filter);
                    $temp[] = $objectName.'.'.$filter[0].' == '.$filter[1];
                }
                $tempString = implode(' && ', $temp);
                return $tempString;
            }, $filters);

            if(!empty($newFilter)){
                $newFilter = ' if '.$newFilter;
            }
        }

        $loopstart = $node->ownerDocument->createTextNode("{% for ".$loopVariables.$newFilter." %}");
        $loopend = $node->ownerDocument->createTextNode("{% endfor %}");
        $nextNode = $node->nextSibling;
        $node->parentNode->insertBefore($loopstart, $node);
        $node->parentNode->insertBefore($loopend, $nextNode);
        return $node;
    }

    protected static function convertNgBindHtmlUnsave(\DOMElement $node, $router){
        $attr = $node->getAttribute('ng-bind-html-unsafe');
        $attr = str_replace(array('[[', ']]', ' '), '', $attr);
        $content = $node->ownerDocument->createTextNode(" {{ ".$attr." |raw }}  ");
        $node->appendChild($content);
    }

    protected static function convertNgBindHtml(\DOMElement $node, $router){
        $attr = $node->getAttribute('ng-bind-html');
        $attr = str_replace(array('[[', ']]', ' '), '', $attr);
        $content = $node->ownerDocument->createTextNode(" {{ ".$attr."  }}  ");
        $node->appendChild($content);
    }
}