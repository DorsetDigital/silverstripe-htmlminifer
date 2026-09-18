<?php

namespace DorsetDigital\SSMinifier;

use SilverStripe\Admin\AdminRootController;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\Middleware\HTTPMiddleware;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use voku\helper\HtmlMin;

class Middleware implements HTTPMiddleware
{
    use Injectable;
    use Configurable;

    /**
     * Enable minification.
     */
    private static bool $enable = false;

    /**
     * Enable minification in dev mode.
     */
    private static bool $enable_in_dev = false;

    /**
     * HtmlMin configuration methods and their arguments.
     */
    private static array $options = [];

    /**
     * HtmlMin methods which may be configured through Silverstripe config.
     */
    private const ALLOWED_OPTIONS = [
        'doMakeSameDomainsLinksRelative',
        'doMinifyJavaScript',
        'doRemoveComments',
        'doRemoveDefaultAttributes',
        'doRemoveDeprecatedAnchorName',
        'doRemoveDeprecatedScriptCharsetAttribute',
        'doRemoveDeprecatedTypeFromScriptTag',
        'doRemoveDeprecatedTypeFromStylesheetLink',
        'doRemoveEmptyAttributes',
        'doRemoveHttpPrefixFromAttributes',
        'doRemoveHttpsPrefixFromAttributes',
        'doRemoveOmittedHtmlTags',
        'doRemoveOmittedQuotes',
        'doRemoveSpacesBetweenTags',
        'doRemoveValueFromEmptyInput',
        'doSortCssClassNames',
        'doSortHtmlAttributes',
        'doSumUpWhitespace',
        'setLocalDomains',
    ];

    public function process(HTTPRequest $request, callable $delegate)
    {
        $response = $delegate($request);

        if (!$response instanceof HTTPResponse || !$this->canRun()) {
            return $response;
        }

        if ($this->getIsAdmin($request) || $this->getIsSitemap($request) || !$this->getIsHtml($response)) {
            return $response;
        }

        $body = $response->getBody();

        if ($body === null || $body === '') {
            return $response;
        }

        $response->setBody($this->getMinifier()->minify($body));

        return $response;
    }

    private function getMinifier(): HtmlMin
    {
        $minifier = new HtmlMin();
        $options = $this->config()->get('options') ?? [];

        foreach ($options as $method => $value) {
            if (!in_array($method, self::ALLOWED_OPTIONS, true) || !method_exists($minifier, $method)) {
                continue;
            }

            $minifier->{$method}($value);
        }

        return $minifier;
    }

    private function canRun(): bool
    {
        $config = $this->config();

        return (bool) $config->get('enable')
            && (!Director::isDev() || (bool) $config->get('enable_in_dev'));
    }

    private function getIsAdmin(HTTPRequest $request): bool
    {
        $adminPath = trim(AdminRootController::admin_url(), '/');
        $currentPath = trim($request->getURL(), '/');

        return $currentPath === $adminPath
            || str_starts_with($currentPath, $adminPath . '/');
    }

    private function getIsSitemap(HTTPRequest $request): bool
    {
        return str_contains(strtolower($request->getURL()), 'sitemap.xml');
    }

    private function getIsHtml(HTTPResponse $response): bool
    {
        $contentType = strtolower((string) $response->getHeader('content-type'));

        return $contentType === ''
            || str_contains($contentType, 'text/html')
            || str_contains($contentType, 'application/xhtml+xml');
    }
}
