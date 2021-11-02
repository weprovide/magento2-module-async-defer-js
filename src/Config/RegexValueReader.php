<?php
declare(strict_types=1);

namespace WeProvide\AsyncDeferJs\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;
use WeProvide\AsyncDeferJs\Exception\Config\InvalidRegexValueException;

/**
 * Responsible for reading the value of a system configuration and performing validation on the regular expression
 */
class RegexValueReader implements ValueReaderInterface
{
    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /** @var LoggerInterface */
    protected $logger;

    /** @var string the XML path where the regular expression in the system configuration is stored */
    protected $path;

    /** @var string the scope to use to determine config value, e.g., 'store' or 'default' */
    protected $scopeType;

    /**
     * RegexValueReader constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     * @param string $path
     * @param string $scopeType
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger,
        string $path,
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->path = $path;
        $this->scopeType = $scopeType;
    }

    /** @inheritDoc */
    public function read(): ?string
    {
        $value = $this->scopeConfig->getValue($this->path, $this->scopeType);

        if (empty($value) || !is_string($value)) {
            return null;
        }

        // contain the configured expression inside slashes to indicate an actual regular expression
        $value = sprintf('/%s/', $value);

        if (!$this->isRegularExpression($value)) {
            throw new InvalidRegexValueException();
        }

        return $value;
    }

    /**
     * Check if a string is a regular expression
     *
     * @param string $str the string to check
     * @return bool true if the string is a regular expression, false otherwise
     */
    protected function isRegularExpression(string $str): bool
    {
        /*
         * Thanks to Sebastion Michaelsen's gist
         * https://gist.github.com/smichaelsen/717fae9055ae83ed8e15
         */

        set_error_handler(function () {
        }, E_WARNING);

        $isRegularExpression = preg_match($str, '') !== false;

        restore_error_handler();
        return $isRegularExpression;
    }
}
