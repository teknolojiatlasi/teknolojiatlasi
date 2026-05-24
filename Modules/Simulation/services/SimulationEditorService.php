<?php

namespace Modules\Simulation\Services;

use Modules\Simulation\Models\Simulation;
use Modules\Simulation\Models\SimulationVersion;

class SimulationEditorService
{
    public function createVersion(Simulation $simulation, array $payload, ?int $userId = null): SimulationVersion
    {
        $latestVersion = (int) $simulation->versions()->max('version');

        return $simulation->versions()->create([
            'version' => $latestVersion + 1,
            'html_code' => (string) ($payload['html_code'] ?? ''),
            'css_code' => (string) ($payload['css_code'] ?? ''),
            'js_code' => (string) ($payload['js_code'] ?? ''),
            'change_note' => $payload['change_note'] ?? null,
            'created_by' => $userId,
        ]);
    }

    public function buildPreviewDocument(string $html, string $css = '', string $js = ''): string
    {
        $html = trim($html);
        $css = trim($css);
        $js = trim($js);

        if ($html !== '' && preg_match('/<html[\s>]/i', $html) === 1) {
            return $html;
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>{$css}</style>
</head>
<body>
{$html}
<script>{$js}</script>
</body>
</html>
HTML;
    }

    public function buildInlineRenderParts(string $html, string $css = '', string $js = ''): array
    {
        $html = trim($html);
        $css = trim($css);
        $js = trim($js);

        if ($html !== '' && preg_match('/<html[\s>]/i', $html) === 1) {
            return [
                'head' => $this->extractHeadAssets($html),
                'body' => $this->extractBodyContent($html),
                'script' => $this->extractScripts($html),
            ];
        }

        $head = $css !== '' ? "<style>\n{$css}\n</style>" : '';
        $script = $js !== '' ? "<script>\n{$js}\n</script>" : '';

        return [
            'head' => $head,
            'body' => $html,
            'script' => $script,
        ];
    }

    private function extractHeadAssets(string $html): string
    {
        if (! preg_match('/<head\b[^>]*>(.*?)<\/head>/is', $html, $matches)) {
            return '';
        }

        $head = $matches[1] ?? '';

        preg_match_all('/<(?:style|link)\b[^>]*>.*?<\/style>|<link\b[^>]*>/is', $head, $assetMatches);

        return trim(implode("\n", $assetMatches[0] ?? []));
    }

    private function extractBodyContent(string $html): string
    {
        if (preg_match('/<body\b[^>]*>(.*?)<\/body>/is', $html, $matches)) {
            $body = $matches[1] ?? '';
        } else {
            $body = $html;
        }

        $body = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $body) ?? $body;

        return trim($body);
    }

    private function extractScripts(string $html): string
    {
        preg_match_all('/<script\b[^>]*>.*?<\/script>/is', $html, $matches);

        return trim(implode("\n", $matches[0] ?? []));
    }
}
