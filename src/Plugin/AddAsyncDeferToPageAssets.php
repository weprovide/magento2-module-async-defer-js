<?php
declare(strict_types=1);

namespace WeProvide\AsyncDeferJs\Plugin;

use Magento\Framework\View\Page\Config\Renderer;
use Psr\Log\LoggerInterface;
use WeProvide\AsyncDeferJs\Config\AsyncRegexValueReader;
use WeProvide\AsyncDeferJs\Config\DeferRegexValueReader;
use WeProvide\AsyncDeferJs\Config\ValueReaderInterface;
use WeProvide\AsyncDeferJs\Exception\Config\ValueReaderException;
use WeProvide\AsyncDeferJs\Factory\HTMLDocumentFactory;

/**
 * Responsible for adding the "async" and "defer" attribute to HTML script elements that match a set of regular expressions
 */
class AddAsyncDeferToPageAssets
{
    /** @var HTMLDocumentFactory */
    protected $htmlDocumentFactory;

    /** @var ValueReaderInterface */
    protected $asyncRegexValueReader;

    /** @var ValueReaderInterface */
    protected $deferRegexValueReader;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * AddAsyncDeferToPageAssets constructor
     *
     * @param HTMLDocumentFactory $htmlDocumentFactory
     * @param ValueReaderInterface $asyncRegexValueReader
     * @param ValueReaderInterface $deferRegexValueReader
     * @param LoggerInterface $logger
     */
    public function __construct(
        HTMLDocumentFactory $htmlDocumentFactory,
        ValueReaderInterface $asyncRegexValueReader,
        ValueReaderInterface $deferRegexValueReader,
        LoggerInterface $logger
    ) {
        $this->htmlDocumentFactory = $htmlDocumentFactory;
        $this->asyncRegexValueReader = $asyncRegexValueReader;
        $this->deferRegexValueReader = $deferRegexValueReader;
        $this->logger = $logger;
    }

    public function afterRenderAssets(Renderer $subject, string $result): string
    {
        $document = $this->htmlDocumentFactory->create($result);

        foreach ($document->querySelectorAll('script') as $script) {
            $src = $script->getAttribute('src');

            if ($src === null) {
                continue;
            }

            if ($this->matchAsyncRegex($src)) {
                $script->setAttribute('async', 'true');
            }

            if ($this->matchDeferRegex($src)) {
                $script->setAttribute('defer', 'true');
            }
        }

        // https://stackoverflow.com/a/10023094/4391861
        return preg_replace('~<(?:!DOCTYPE|/?(?:html|body|head))[^>]*>\s*~i', '', $document->saveHTML());
    }

    /**
     * Check if the given source matches the regular expression for adding the "async" HTML attribute
     *
     * @param string $src the source to match
     * @return bool true if the given source matches the regular expression, false otherwise
     */
    protected function matchAsyncRegex(string $src): bool
    {
        try {
            $regex = $this->asyncRegexValueReader->read();
            return $regex !== null && preg_match($regex, $src) === 1;
        } catch (ValueReaderException $exception) {
            $this->logger->warning(sprintf(
                'Something went wrong whilst reading the regular expression for the "async" HTML attribute: %s',
                $exception->getMessage()
            ));

            return false;
        }
    }

    /**
     * Check if the given source matches the regular expression for adding the "defer" HTML attribute
     *
     * @param string $src the source to match
     * @return bool true if the given source matches the regular expression, false otherwise
     */
    protected function matchDeferRegex(string $src): bool
    {
        try {
            $regex = $this->deferRegexValueReader->read();
            return $regex !== null && preg_match($regex, $src) === 1;
        } catch (ValueReaderException $exception) {
            $this->logger->warning(sprintf(
                'Something went wrong whilst reading the regular expression for the "defer" HTML attribute: %s',
                $exception->getMessage()
            ));

            return false;
        }
    }
}
