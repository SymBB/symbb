<?php

namespace Symbb\Core\SystemBundle;

class Utils {

    public static function purifyHtml($html){
        $config = \HTMLPurifier_Config::createDefault();
        $purifier = new \HTMLPurifier($config);
        $html = nl2br($html);
        $cleanHtml = $purifier->purify($html);
        return $cleanHtml;
    }

    public static function removeHtml($html){
        $html = htmlspecialchars(strip_tags($html));
        return $html;
    }

}