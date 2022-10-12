<?php
declare(strict_types=1);

namespace WeProvide\AsyncDeferJs\Plugin;

use Magento\Framework\View\Page\Config\Renderer;
use WeProvide\AsyncDeferJs\Service\ScriptAdjuster;

/**
 * Responsible for adding the "async" and "defer" attribute to HTML script elements that match a set of regular
 * expressions
 */
class AddAsyncDeferToPageAssets
{
    /** @var ScriptAdjuster */
    protected ScriptAdjuster $scriptAdjuster;

    /**
     * @param ScriptAdjuster $scriptAdjuster
     */
    public function __construct(ScriptAdjuster $scriptAdjuster)
    {
        $this->scriptAdjuster = $scriptAdjuster;
    }

    /**
     * Gives the result to the script adjuster to apply async and defer
     *
     * @param Renderer $subject
     * @param string $result
     * @return string
     */
    public function afterRenderAssets(Renderer $subject, string $result): string
    {
        return $this->scriptAdjuster->adjustScripts($result);
    }
}
