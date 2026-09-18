<?php

namespace DorsetDigital\SSMinifier\Tests;

use DorsetDigital\SSMinifier\Middleware;
use PHPUnit\Framework\TestCase;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Config\Config;

class MiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::modify()->set(Middleware::class, 'enable', true);
        Config::modify()->set(Middleware::class, 'enable_in_dev', true);
    }

    public function testHtmlResponseIsMinified(): void
    {
        $response = $this->process(
            '/',
            '<html>\n    <body>\n        <p>Hello world</p>\n    </body>\n</html>',
            'text/html; charset=utf-8'
        );

        $this->assertStringNotContainsString("\n", $response->getBody());
        $this->assertStringContainsString('<p>Hello world</p>', $response->getBody());
    }

    public function testJsonResponseIsNotMinified(): void
    {
        $body = "{\n    \"message\": \"Hello world\"\n}";

        $response = $this->process('/api', $body, 'application/json');

        $this->assertSame($body, $response->getBody());
    }

    public function testAdminResponseIsNotMinified(): void
    {
        $body = "<html>\n    <body>Admin</body>\n</html>";

        $response = $this->process('/admin/pages', $body, 'text/html');

        $this->assertSame($body, $response->getBody());
    }

    public function testSitemapResponseIsNotMinified(): void
    {
        $body = "<?xml version=\"1.0\"?>\n<urlset>\n    <url></url>\n</urlset>";

        $response = $this->process('/sitemap.xml', $body, 'application/xml');

        $this->assertSame($body, $response->getBody());
    }

    private function process(string $url, string $body, string $contentType): HTTPResponse
    {
        $request = new HTTPRequest('GET', $url);
        $response = new HTTPResponse($body);
        $response->addHeader('Content-Type', $contentType);

        return Middleware::create()->process($request, static fn () => $response);
    }
}
