<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'subject',
        'body_html',
        'body_text',
        'available_variables',
        'is_active',
        'category',
        'version',
    ];

    protected $casts = [
        'available_variables' => 'array',
        'is_active' => 'boolean',
        'version' => 'integer',
    ];

    // Query scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByName($query, string $name)
    {
        return $query->where('name', $name);
    }

    // Render template with variables
    public function render(array $variables = []): array
    {
        $subject = $this->renderString($this->subject, $variables);
        $bodyHtml = $this->renderString($this->body_html, $variables);
        $bodyText = $this->body_text ? $this->renderString($this->body_text, $variables) : null;

        return [
            'subject' => $subject,
            'body_html' => $bodyHtml,
            'body_text' => $bodyText,
        ];
    }

    // Replace variables in string
    private function renderString(string $template, array $variables): string
    {
        $output = $template;
        
        foreach ($variables as $key => $value) {
            // Support both {{variable}} and {variable} syntax
            $output = str_replace([
                '{{' . $key . '}}',
                '{' . $key . '}',
            ], $value, $output);
        }

        return $output;
    }

    // Validate that all required variables are provided
    public function validateVariables(array $providedVariables): array
    {
        $required = $this->available_variables ?? [];
        $missing = [];

        foreach ($required as $variable) {
            if (!isset($providedVariables[$variable])) {
                $missing[] = $variable;
            }
        }

        return $missing;
    }

    // Check if template has variable
    public function hasVariable(string $variable): bool
    {
        return in_array($variable, $this->available_variables ?? []);
    }
}
