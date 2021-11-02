<?php
declare(strict_types=1);

namespace WeProvide\AsyncDeferJs\Test\Unit\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use WeProvide\AsyncDeferJs\Exception\Config\InvalidRegexValueException;

class RegexValueReader extends TestCase
{
    /**
     * @param string $value
     * @param string|null $expected
     * @param string|null $exception
     *
     * @dataProvider dataProviderRegex
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function testReadValidRegexValue(string $value, ?string $expected, ?string $exception): void
    {
        $scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)->getMock();
        $scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with($this->equalTo('our/regex'))
            ->willReturn($value);

        $regexValueReader = new \WeProvide\AsyncDeferJs\Config\RegexValueReader(
            $scopeConfigMock,
            $this->getMockBuilder(LoggerInterface::class)->getMock(),
            'our/regex'
        );

        if ($expected !== null) {
            $this->assertEquals($expected, $regexValueReader->read());
        } elseif ($exception !== null) {
            $this->expectException($exception);
            $regexValueReader->read();
        } else {
            $this->fail('data provider contains entry with neither an expected result nor an exception');
        }
    }

    public function dataProviderRegex(): array
    {
        return [
            [ '.*', '/.*/', null ],
            [ '.[*', null, InvalidRegexValueException::class ]
        ];
    }
}
