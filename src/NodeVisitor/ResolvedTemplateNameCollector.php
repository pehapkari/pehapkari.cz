<?php

declare(strict_types=1);

namespace Pehapkari\NodeVisitor;

final class ResolvedTemplateNameCollector
{
    /**
     * @var string
     */
    private $templateName;

    public function setValue(string $templateName): void
    {
        $this->templateName = $templateName;
    }

    public function getTemplateName(): string
    {
        return $this->templateName;
    }
}
