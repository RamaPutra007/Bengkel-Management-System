<?php

namespace App\Services;

use App\Models\WhatsappTemplate;
use Illuminate\Support\Facades\Log;

class WhatsAppTemplateService
{
    /**
     * Get active template by code
     */
    public function getTemplate(string $code): ?WhatsappTemplate
    {
        return WhatsappTemplate::where('code', $code)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Validate if all variables required by the template are provided
     */
    public function validateVariables(WhatsappTemplate $template, array $data): bool
    {
        $variables = $template->variables ?? [];
        
        foreach ($variables as $variable) {
            if (!array_key_exists($variable, $data)) {
                Log::warning("Missing variable '{$variable}' for template '{$template->code}'");
            }
        }
        
        return true; // We don't block rendering if some are missing, just log it.
    }

    /**
     * Replace variables in template content
     */
    public function replaceVariables(string $content, array $data): string
    {
        foreach ($data as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }
        return $content;
    }

    /**
     * Render the template with given variables
     */
    public function render(string $code, array $data): ?string
    {
        $template = $this->getTemplate($code);
        
        if (!$template) {
            Log::error("Template '{$code}' not found or inactive.");
            return null;
        }

        $this->validateVariables($template, $data);
        
        return $this->replaceVariables($template->content, $data);
    }
}
