# SymBB Forum System


More detailed information and instructions can be found here (currently only in German):

https://github.com/seyon/symbb/wiki/

For a "finished" Symfony version you can use the Sandbox :

https://github.com/seyon/symbb_sandbox

[![Build Status](https://travis-ci.org/seyon/symbb.png?branch=master)](https://travis-ci.org/seyon/symbb)
[![Latest Stable Version](https://poser.pugx.org/symbb/symbb/v/stable.png)](https://packagist.org/packages/symbb/symbb)
[![Latest Unstable Version](https://poser.pugx.org/symbb/symbb/v/unstable.png)](https://packagist.org/packages/symbb/symbb)
[![Total Downloads](https://poser.pugx.org/symbb/symbb/downloads.png)](https://packagist.org/packages/symbb/symbb)

# Bundles are used

- FOSUserBundle ( optional, but recommended )
- FOSRestBundle ( for future api )
- FOSJsRoutingBundle 
- FOSMessageBundle (PM System)
- KnpMenuBundle
- KnpPaginatorBundle
- SonataIntlBundle ( Date formating )
- FMBbcodeBundle (BBCodes)
- LswMemcacheBundle (Memcache Manager)

# Demo

A demo of the latest features can be found here :

http://symbb.de/


-------
Ideensammlung:

- Topics können an eine andere Person übergeben werden ( muss jedoch bestätigt werden, bzw. die neue person kann es "beantragen" )
- "Editiert bei" historie verfügbar machen ( keine genauen infos nur wer und wann, ggf. noch "grund des editierens" als feld beim editieren anlegen )


-------
php app/console translation:extract de -c symbb