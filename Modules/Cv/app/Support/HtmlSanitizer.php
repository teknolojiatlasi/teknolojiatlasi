<?php

namespace Modules\Cv\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

class HtmlSanitizer
{
    /**
     * Returns a safe HTML fragment suitable for rendering with `{!! !!}`.
     */
    public static function sanitize(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        $html = trim($html);
        if ($html === '') {
            return null;
        }

        // If it's plain text, escape and preserve line breaks.
        if (!str_contains($html, '<')) {
            return nl2br(htmlspecialchars($html, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
        }

        $allowedTags = [
            'p' => true,
            'br' => true,
            'strong' => true,
            'b' => true,
            'em' => true,
            'i' => true,
            'u' => true,
            'ul' => true,
            'ol' => true,
            'li' => true,
            'h1' => true,
            'h2' => true,
            'h3' => true,
            'h4' => true,
            'a' => true,
        ];

        $dom = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);

        // Wrap as a fragment so we can safely return inner HTML.
        $wrapped = '<div>' . $html . '</div>';
        $dom->loadHTML('<?xml encoding="UTF-8">' . $wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $container = $dom->getElementsByTagName('div')->item(0);
        if (!$container instanceof DOMElement) {
            return htmlspecialchars(strip_tags($html), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        self::sanitizeChildren($container, $allowedTags);

        $output = '';
        foreach (iterator_to_array($container->childNodes) as $child) {
            $output .= $dom->saveHTML($child);
        }

        return trim($output) ?: null;
    }

    /**
     * @param array<string,true> $allowedTags
     */
    private static function sanitizeChildren(DOMNode $parent, array $allowedTags): void
    {
        foreach (iterator_to_array($parent->childNodes) as $child) {
            if ($child->nodeType === XML_COMMENT_NODE) {
                $parent->removeChild($child);
                continue;
            }

            if ($child instanceof DOMElement) {
                $tag = strtolower($child->tagName);

                if (!isset($allowedTags[$tag])) {
                    if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed'], true)) {
                        $parent->removeChild($child);
                        continue;
                    }

                    self::unwrapNode($child);
                    continue;
                }

                self::sanitizeElementAttributes($child, $tag);
                self::sanitizeChildren($child, $allowedTags);
            }
        }
    }

    private static function unwrapNode(DOMElement $element): void
    {
        $parent = $element->parentNode;
        if (!$parent) {
            return;
        }

        while ($element->firstChild) {
            $parent->insertBefore($element->firstChild, $element);
        }

        $parent->removeChild($element);
    }

    private static function sanitizeElementAttributes(DOMElement $element, string $tag): void
    {
        $href = null;
        if ($tag === 'a' && $element->hasAttribute('href')) {
            $href = self::sanitizeHref($element->getAttribute('href'));
        }

        foreach (iterator_to_array($element->attributes ?? []) as $attr) {
            $element->removeAttribute($attr->nodeName);
        }

        if ($tag === 'a' && $href !== null) {
            $element->setAttribute('href', $href);
            $element->setAttribute('rel', 'nofollow noopener noreferrer');
        }
    }

    private static function sanitizeHref(string $href): ?string
    {
        $href = trim($href);
        if ($href === '') {
            return null;
        }

        $decoded = strtolower(html_entity_decode($href, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));

        if (str_starts_with($decoded, 'javascript:') || str_starts_with($decoded, 'data:') || str_starts_with($decoded, 'vbscript:')) {
            return null;
        }

        if (
            str_starts_with($href, '#') ||
            str_starts_with($href, '/') ||
            preg_match('/^(https?:|mailto:|tel:)/i', $href)
        ) {
            return $href;
        }

        return null;
    }
}
