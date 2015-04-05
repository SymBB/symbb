# SymBB Forum System

[![Join the chat at https://gitter.im/SymBB/symbb](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/SymBB/symbb?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Latest Stable Version](https://poser.pugx.org/symbb/symbb/v/stable.png)](https://packagist.org/packages/symbb/symbb)
[![Latest Unstable Version](https://poser.pugx.org/symbb/symbb/v/unstable.png)](https://packagist.org/packages/symbb/symbb)
[![Total Downloads](https://poser.pugx.org/symbb/symbb/downloads.png)](https://packagist.org/packages/symbb/symbb)

### Sandbox Version

You can find the Sandbox Version under this url:
https://github.com/seyon/symbb_sandbox

The Sandbox Version has a finished setup for the SymBB Forum.
You can use it to try it out, but dont use it for projects. The System is not finished and not stabel.

### Wiki

I will add the wiki later after the first pre stable version

### Important Notes

Currently the SymBB System force SF to use special configurations!

It defines some Framework/Doctrine/... configurations and you can not override it with the global config file!

So if you need to change configurations disable the Symbb/Core/ConfigBundle and replace it with a extended version who dont add the Configfiles. But in this case you must configure all the stuff manually.

---------------------------------

# Notices for me

## Utils

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


## Sending Emails

The "system.email" must be the same domain as the "mailer_host"
