<?php
declare(strict_types=1);

namespace WeProvide\AsyncDeferJs\Plugin;

use Magento\Framework\View\Element\AbstractBlock;
use WeProvide\AsyncDeferJs\Service\Config as ConfigService;
use WeProvide\AsyncDeferJs\Service\ScriptAdjuster;

/**
 * Responsible for adding the "async" and "defer" attribute to HTML script elements that match a set of regular
 * expressions
 */
class AddAsyncDeferToBlockScripts
{
    /** @var ScriptAdjuster */
    protected ScriptAdjuster $scriptAdjuster;
    /** @var ConfigService */
    protected ConfigService $configService;

    /**
     * @param ScriptAdjuster $scriptAdjuster
     * @param ConfigService $configService
     */
    public function __construct(ScriptAdjuster $scriptAdjuster, ConfigService $configService)
    {
        $this->scriptAdjuster = $scriptAdjuster;
        $this->configService  = $configService;
    }

    /**
     * Gives the result to the script adjuster to apply async and defer
     *
     * @param AbstractBlock $subject
     * @param string $result
     * @return string
     */
    public function afterToHtml(AbstractBlock $subject, string $result): string
    {
        if ($this->configService->shouldCheckBlockScript()) {
            $result = $this->scriptAdjuster->adjustScripts($result);
        }

        return $result;
    }
}
