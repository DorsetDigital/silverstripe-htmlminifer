<?php

namespace DorsetDigital\SSMinify;

use SilverStripe\Admin\AdminRootController;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\Middleware\HTTPMiddleware;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\View\HTML;
use voku\helper\HtmlMin;

class Middleware implements HTTPMiddleware
{

    use Injectable;
    use Configurable;

    /**
     * @config
     *
     * Enable minification
     * @var bool
     */
    private static $enabled = false;


    /**
     * @config
     *
     * Enable in dev mode
     * @var bool
     */
    private static $enable_in_dev = false;


    /**
     * Process the request
     * @param HTTPRequest $request
     * @param $delegate
     * @return
     */
    public function process(HTTPRequest $request, callable $delegate)
    {
        $response = $delegate($request);

        if (($this->canRun() === true) && ($response !== null)) {

            if ($this->getIsAdmin($request) === false) {
                $body = $response->getBody();
                $minifier = new HtmlMin();
                $minBody = $minifier->minify($body);
                $response->setBody($minBody);
            }

        }

        return $response;
    }

    /**
     * Check if we're OK to execute
     * @return bool
     */
    private function canRun()
    {
        $confEnabled = $this->config()->get('cdn_rewrite');
        $devEnabled = ((!Director::isDev()) || ($this->config()->get('enable_in_dev')));
        return ($confEnabled && $devEnabled);
    }


    /**
     * Determine whether the website is being viewed from an admin protected area or not
     * (shamelessly based on https://github.com/silverstripe/silverstripe-subsites)
     *
     * @param HTTPRequest $request
     * @return bool
     */
    private function getIsAdmin(HTTPRequest $request)
    {
        $adminPath = AdminRootController::admin_url();
        $currentPath = rtrim($request->getURL(), '/') . '/';
        if (substr($currentPath, 0, strlen($adminPath)) === $adminPath) {
            return true;
        }
        return false;
    }
}
