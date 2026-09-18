# Silverstripe HTML Minifier

This module minifies the HTML output of a Silverstripe site using the HtmlMin package.

## Requirements

- Silverstripe CMS 6.x
- PHP 8.3+

For Silverstripe CMS 4 projects, use the `cms4` branch.

## Installation

Install the module with Composer:

```bash
composer require dorsetdigital/silverstripe-htmlminifier
```

Then flush your Silverstripe configuration cache.

## Usage

The module is disabled by default. Enable it in your project configuration:

```yaml
---
Name: minifierconfig
---

DorsetDigital\SSMinifier\Middleware:
  enable: true
  enable_in_dev: false
```

The available options are:

- `enable` - enable HTML minification.
- `enable_in_dev` - enable minification in dev mode. Defaults to `false`.

## Notes

- CMS/admin requests are not minified.
- XML sitemap responses are not minified.
- Only HTML responses are minified.

## Credits

- Uses the excellent HtmlMin package: https://github.com/voku/HtmlMin
- As always, thanks to the Silverstripe core team for all their hard work.
