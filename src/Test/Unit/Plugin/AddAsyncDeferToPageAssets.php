<?php
declare(strict_types=1);

namespace WeProvide\AsyncDeferJs\Test\Unit\Plugin;

use Gt\Dom\HTMLDocument;
use Magento\Framework\View\Page\Config\Renderer;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use WeProvide\AsyncDeferJs\Config\ValueReaderInterface;
use WeProvide\AsyncDeferJs\Factory\HTMLDocumentFactory;

class AddAsyncDeferToPageAssets extends TestCase
{
    public function testAddAsyncDefer()
    {
        $asyncValueReader = $this->getMockBuilder(ValueReaderInterface::class)->getMock();
        $asyncValueReader->expects($this->any())
            ->method('read')
            ->willReturn('/example\.com/');

        $deferValueReader = $this->getMockBuilder(ValueReaderInterface::class)->getMock();
        $deferValueReader->expects($this->any())
            ->method('read')
            ->willReturn('/foo\.bar/');

        $plugin = new \WeProvide\AsyncDeferJs\Plugin\AddAsyncDeferToPageAssets(
            new HTMLDocumentFactory(),
            $asyncValueReader,
            $deferValueReader,
            $this->getMockBuilder(LoggerInterface::class)->getMock()
        );

        $html = /** @lang text */ '
            <script type="type/javascript" src="https://www.example.com/1.js"></script>
            <script type="type/javascript" src="https://www.foo.bar/2.js"></script>
            <link rel="stylesheet" type="text/css" href="https://www.example.com/1.css">
            <script type="type/javascript" src="https://www.example.com/3.js"></script>
            <script type="type/javascript" src="https://www.foo.bar/4.js"></script>
            <link rel="stylesheet" type="text/css" href="https://www.example.com/2.css">
        ';

        $result = $plugin->afterRenderAssets(
            $this->getMockBuilder(Renderer::class)->disableOriginalConstructor()->getMock(),
            $html
        );

        $document = new HTMLDocument($result);
        $script = $document->querySelector('script[src="https://www.example.com/1.js"]');

        $this->assertEquals(trim(/** @lang text */'
            <script type="type/javascript" src="https://www.example.com/1.js" async="true"></script>
            <script type="type/javascript" src="https://www.foo.bar/2.js" defer></script>
            <link rel="stylesheet" type="text/css" href="https://www.example.com/1.css">
            <script type="type/javascript" src="https://www.example.com/3.js" async="true"></script>
            <script type="type/javascript" src="https://www.foo.bar/4.js" defer></script>
            <link rel="stylesheet" type="text/css" href="https://www.example.com/2.css">
        '), trim($result));
    }
}
