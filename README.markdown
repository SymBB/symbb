# SymBB Forum System

## Currently in development! / Derzeit in Entwicklung


### Sandbox Version

You can find the Sandbox Version under this url:
https://github.com/seyon/symbb_sandbox

The Sandbox Version has a finished setup for the SymBB Forum.
You can use it to try it out, but dont use it for projects. The System is not finished and not stabel.

### Wiki

I have removed the Wiki because i change to much stuff.
I will add the Wiki back after i release a first "RC" Version.

### Questions

If you have Questions please use the Github Bugtracker. But it can be need some time to answer you.
The most of my free time will be used to develop the System. Eventually i dont find time to answer to your Question.
Please note this is only the case as long no "RC" Version or "Beta" Version is released. After that i will give you better Support :)

### Important Notes

Currently the SymBB System force SF to use special configurations!
It defines some Framework/Doctrine/... configurations and you can not override it with the global config file!
So if you need to change configurations disable the Symbb/Core/ConfigBundle and replace it with a extended version who dont add the Configfiles. But in this case you must configure all the stuff manually.

[![Build Status](https://travis-ci.org/seyon/symbb.png?branch=master)](https://travis-ci.org/seyon/symbb)
[![Latest Stable Version](https://poser.pugx.org/symbb/symbb/v/stable.png)](https://packagist.org/packages/symbb/symbb)
[![Latest Unstable Version](https://poser.pugx.org/symbb/symbb/v/unstable.png)](https://packagist.org/packages/symbb/symbb)
[![Total Downloads](https://poser.pugx.org/symbb/symbb/downloads.png)](https://packagist.org/packages/symbb/symbb)

### Demo

In Future you will see a Version of the System on www.symbb.de
Currently you will one get an error because if havent updated it to the last Version.
Until i release a Beta/RC Version the domain will be stay empty.

Edit: 
you can find a version here (http://lets-playrust.de/de/forum/). It is a little bit different. It has only a Steam Login and a custom Template. But to check out some Features you can use look into it. So it is not a "Demo" because the site will be lunched in some weeks. So please dont post testing Stuff.


## Notices for me

### create Translation files

php app/console translation:extract de -c symbb

### Update Steps

- composer update / git update
- rm -r app/cache/*
- rm -r app/logs/*
- php app/console doctrine:schema:update --force --em=symbb
- php app/console fos:js-routing:dump
- php app/console assets:install
- php app/console assetic:dump --env=prod
