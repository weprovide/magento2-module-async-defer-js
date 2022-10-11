<?php

declare(strict_types=1);

namespace WeProvide\AsyncDeferJs\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    /** @var string */
    protected const CONFIG_APPLY_TO_BLOCKS  = 'dev/js/apply_async_defer_to_blocks';

    /** @var ScopeConfigInterface */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Whether blocks should be checked on scripts as well
     *
     * @return bool
     */
    public function shouldCheckBlockScript(): bool
    {
        return $this->scopeConfig->isSetFlag(
            static::CONFIG_APPLY_TO_BLOCKS,
            ScopeInterface::SCOPE_STORE
        );
    }
}
