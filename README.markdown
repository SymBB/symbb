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


## API Structure

### Api Controller

- Api Controller will define the Routing with Annotations
- Api Controller will convert Request data to Objects
- Api Controller will conver Objects to Api Array and pass it to the Json Response
- Routings will not have any Parameters in the URL alls Stuff need to provided via POST/GET
- Every Routing must force to use GET/POST or DELETE

### Api Class

- Api Class will handle all Actions of the different Api Calls
- Api Class will extend from the Abstract Api Class
- Api Class will fire Events

### Api Response

- Every Response should have the elements of the AbstractApi Class ( Errors etc.. )
- Every Response should habe a "data" element with the Object or the Object List
- If the Response is a Success Save response Data should be the saved Element ( so that we have the new id etc.. )
- In case of an error data should be empty

### Api Request

- Every Request to the Api should have a "data" element with the Object to save, the List to save etc...
- Also a delete request should put the id into the data element

## Angular Routings

### Configuration

Every Angular Routing should be configured over the SF Routing.
Add a Routing with special Options:

      symbb_angular_section: acp
      symbb_angular_controller: SiteListCtrl
      symbb_angular_api_route: symbb_backend_api_site_list
      symbb_angular_template:
          route: symbb_template_acp_angular
          params:
              file: 'Site|list'

#### symbb_angular_section

this is optional, it define the section. You can export every section seperatly to the template.
So you can split frontend and Backend Routings, to reduce the size

#### symbb_angular_controller

This option defined what Angular Controller should be called. Leave it empty to use the DefaultApiCtrl

#### symbb_angular_api_route

This defines what api should be called to get the data ( in case of DefaultApiCtrl )

#### symbb_angular_template

This defined what template should be called for this routing. You can also pass some Params
The Template Routing should be defined in the Template Bundle
