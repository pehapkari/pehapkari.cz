<?php

declare(strict_types=1);

namespace Pehapkari\Github\Collector;

final class ResolvedTemplateNameCollector
{
    private ?string $templateName = null;

    public function setValue(string $templateName): void
    {
        $this->templateName = $templateName;
    }

    public function getTemplateName(): ?string
    {
        return $this->templateName;
    }
}
