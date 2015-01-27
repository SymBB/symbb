<?php

namespace Symbb\Core\SystemBundle;

class Utils {

    public static function purifyHtml($html){
        $config = \HTMLPurifier_Config::createDefault();
        $purifier = new \HTMLPurifier($config);
        $cleanHtml = $purifier->purify($html);
        return $cleanHtml;
    }

}