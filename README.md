# Silverstripe HTML Minifier

This module minifies the output of a Silverstripe site using the HtmlMin package

# Requirements
*Silverstripe 4.x

# Installation
* Install the code with `composer require dorsetdigital/silverstripe-htmlminifier "^1"`
* Run a `dev/build?flush` to update your project

# Usage

The module needs to be enabled in order to work. This can be done in a yml file:


```yaml
---
Name: minifierconfig
---

DorsetDigital\SSMinifier\Middleware:
  enable: true
  enable_in_dev: false
```

The options are hopefully fairly self explanatory:

* `enable` - enable minification 
* `enable_in_dev` - enable minification in dev mode (default false)

# Notes

* The module is disabled in the CMS / admin system



# Credits
* Uses the excellent HtmlMin package (https://github.com/voku/HtmlMin)
* As always, thanks to the core team for all their hard work.  
