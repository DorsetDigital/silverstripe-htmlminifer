# Silverstripe HTML Minifier

This module minifies the HTML output of a Silverstripe site using the [HtmlMin](https://github.com/voku/HtmlMin) package.

## Requirements

The current major version supports:

- Silverstripe CMS 6.x
- PHP 8.3+

Silverstripe CMS 4 and 5 are supported by the `1.x` release line.

## Installation

For Silverstripe CMS 6:

```bash
composer require dorsetdigital/silverstripe-htmlminifier:^2
```

For Silverstripe CMS 4 or 5:

```bash
composer require dorsetdigital/silverstripe-htmlminifier:^1
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

The module configuration options are:

- `enable` - enable HTML minification. Defaults to `false`.
- `enable_in_dev` - enable minification while Silverstripe is running in dev mode. Defaults to `false`.
- `options` - optional configuration passed to HtmlMin.

If no `options` are supplied, HtmlMin's own defaults are used.

## HtmlMin options

HtmlMin provides a number of options for controlling the minification process. These can be configured directly through Silverstripe YAML using the method name as the option key.

For example:

```yaml
DorsetDigital\SSMinifier\Middleware:
  enable: true
  options:
    doRemoveComments: true
    doRemoveOmittedQuotes: true
    doRemoveOmittedHtmlTags: true
    doSortCssClassNames: true
    doSortHtmlAttributes: true
    doMinifyJavaScript: false
```

Only options explicitly supported by this module are passed to HtmlMin. Unsupported or unknown options are ignored.

The following HtmlMin options are supported:

- `doMakeSameDomainsLinksRelative`
- `doMinifyJavaScript`
- `doRemoveComments`
- `doRemoveDefaultAttributes`
- `doRemoveDeprecatedAnchorName`
- `doRemoveDeprecatedScriptCharsetAttribute`
- `doRemoveDeprecatedTypeFromScriptTag`
- `doRemoveDeprecatedTypeFromStylesheetLink`
- `doRemoveEmptyAttributes`
- `doRemoveHttpPrefixFromAttributes`
- `doRemoveHttpsPrefixFromAttributes`
- `doRemoveOmittedHtmlTags`
- `doRemoveOmittedQuotes`
- `doRemoveSpacesBetweenTags`
- `doRemoveValueFromEmptyInput`
- `doSortCssClassNames`
- `doSortHtmlAttributes`
- `doSumUpWhitespace`

HtmlMin's `setLocalDomains` option is also supported and accepts an array of domain names. It can be used with `doMakeSameDomainsLinksRelative`:

```yaml
DorsetDigital\SSMinifier\Middleware:
  enable: true
  options:
    setLocalDomains:
      - example.com
      - www.example.com
    doMakeSameDomainsLinksRelative: true
```

The values are passed directly to HtmlMin, so its defaults and behaviour remain authoritative. See the [HtmlMin documentation](https://github.com/voku/HtmlMin) for details of each option.

## Notes

- CMS/admin requests are not minified.
- XML sitemap responses are not minified.
- Only HTML and XHTML responses are minified.
- Responses without a Content-Type header are treated as HTML for backwards compatibility.
- JavaScript minification is controlled by HtmlMin and is not enabled by this module unless configured.

## Development

Install the development dependencies and run the test suite with:

```bash
composer install
composer test
```

The test suite covers HTML minification and ensures that non-HTML, CMS/admin and sitemap responses are left untouched.

## Credits

- Uses the excellent [HtmlMin](https://github.com/voku/HtmlMin) package.
- As always, thanks to the Silverstripe core team for all their hard work.
