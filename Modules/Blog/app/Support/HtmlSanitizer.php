<?php

namespace Modules\Blog\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

class HtmlSanitizer
{
    public static function sanitize(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        $html = trim($html);
        if ($html === '') {
            return null;
        }

        if (! str_contains($html, '<')) {
            return nl2br(htmlspecialchars($html, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
        }

        $allowedTags = [
            'p' => true,
            'div' => true,
            'br' => true,
            'span' => true,
            'strong' => true,
            'b' => true,
            'em' => true,
            'i' => true,
            'u' => true,
            's' => true,
            'strike' => true,
            'sub' => true,
            'sup' => true,
            'ul' => true,
            'ol' => true,
            'li' => true,
            'h1' => true,
            'h2' => true,
            'h3' => true,
            'h4' => true,
            'h5' => true,
            'h6' => true,
            'blockquote' => true,
            'code' => true,
            'pre' => true,
            'hr' => true,
            'a' => true,
            'img' => true,
            'figure' => true,
            'figcaption' => true,
            'table' => true,
            'thead' => true,
            'tbody' => true,
            'tfoot' => true,
            'tr' => true,
            'th' => true,
            'td' => true,
            'colgroup' => true,
            'col' => true,
        ];

        $dom = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);

        $wrapped = '<div>' . $html . '</div>';
        $dom->loadHTML('<?xml encoding="UTF-8">' . $wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $container = $dom->getElementsByTagName('div')->item(0);
        if (! $container instanceof DOMElement) {
            return htmlspecialchars(strip_tags($html), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        self::sanitizeChildren($container, $allowedTags);

        $output = '';
        foreach (iterator_to_array($container->childNodes) as $child) {
            $output .= $dom->saveHTML($child);
        }

        return trim($output) ?: null;
    }

    private static function sanitizeChildren(DOMNode $parent, array $allowedTags): void
    {
        foreach (iterator_to_array($parent->childNodes) as $child) {
            if ($child->nodeType === XML_COMMENT_NODE) {
                $parent->removeChild($child);
                continue;
            }

            if (! $child instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($child->tagName);

            if (! isset($allowedTags[$tag])) {
                if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'form'], true)) {
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

    private static function unwrapNode(DOMElement $element): void
    {
        $parent = $element->parentNode;
        if (! $parent) {
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
        $src = null;
        $alt = null;
        $title = null;
        $style = null;
        $class = null;
        $preservedDataAttrs = [];
        $preservedTableAttrs = [];

        if ($tag === 'a' && $element->hasAttribute('href')) {
            $href = self::sanitizeHref($element->getAttribute('href'));
        }

        if ($tag === 'img' && $element->hasAttribute('src')) {
            $src = self::sanitizeSrc($element->getAttribute('src'));
            $alt = $element->getAttribute('alt');
            $title = $element->getAttribute('title');
        }

        if ($element->hasAttribute('style')) {
            $style = self::sanitizeInlineStyle($element->getAttribute('style'));
        }

        if ($element->hasAttribute('class')) {
            $class = self::sanitizeClassNames($element->getAttribute('class'));
        }

        foreach (iterator_to_array($element->attributes ?? []) as $attr) {
            $name = strtolower($attr->nodeName);
            $value = trim($attr->nodeValue ?? '');

            if (str_starts_with($name, 'data-') && $value !== '') {
                $preservedDataAttrs[$name] = $value;
            }

            if (in_array($tag, ['td', 'th'], true) && in_array($name, ['colspan', 'rowspan'], true) && preg_match('/^\d+$/', $value)) {
                $preservedTableAttrs[$name] = $value;
            }

            if ($tag === 'col' && $name === 'span' && preg_match('/^\d+$/', $value)) {
                $preservedTableAttrs[$name] = $value;
            }

            $element->removeAttribute($attr->nodeName);
        }

        if ($tag === 'a' && $href !== null) {
            $element->setAttribute('href', $href);
            $element->setAttribute('rel', 'nofollow noopener noreferrer');
        }

        if ($tag === 'img' && $src !== null) {
            $element->setAttribute('src', $src);

            if ($alt !== null && trim($alt) !== '') {
                $element->setAttribute('alt', trim($alt));
            }

            if ($title !== null && trim($title) !== '') {
                $element->setAttribute('title', trim($title));
            }

            $element->setAttribute('loading', 'lazy');
        }

        if ($class !== null) {
            $element->setAttribute('class', $class);
        }

        if ($style !== null) {
            $element->setAttribute('style', $style);
        }

        foreach ($preservedDataAttrs as $name => $value) {
            $element->setAttribute($name, $value);
        }

        foreach ($preservedTableAttrs as $name => $value) {
            $element->setAttribute($name, $value);
        }
    }

    private static function sanitizeInlineStyle(string $style): ?string
    {
        $style = trim($style);
        if ($style === '') {
            return null;
        }

        $allowedStyles = [];

        foreach (explode(';', $style) as $declaration) {
            $declaration = trim($declaration);
            if ($declaration === '' || ! str_contains($declaration, ':')) {
                continue;
            }

            [$property, $value] = array_map('trim', explode(':', $declaration, 2));
            $property = strtolower($property);

            if (! self::isAllowedStyle($property, $value)) {
                continue;
            }

            $allowedStyles[] = $property . ': ' . $value;
        }

        return $allowedStyles !== [] ? implode('; ', $allowedStyles) : null;
    }

    private static function isAllowedStyle(string $property, string $value): bool
    {
        $colorProps = ['color', 'background-color', 'border-color'];
        $sizeProps = ['width', 'height', 'max-width', 'min-width', 'min-height'];
        $spacingProps = ['margin', 'margin-left', 'margin-right', 'padding'];

        if (in_array($property, $colorProps, true)) {
            return self::isSafeCssColor($value);
        }

        if (in_array($property, $sizeProps, true)) {
            return self::isSafeCssSize($value);
        }

        if (in_array($property, $spacingProps, true)) {
            return self::isSafeCssSpacing($value);
        }

        if ($property === 'text-align') {
            return in_array(strtolower($value), ['left', 'right', 'center', 'justify'], true);
        }

        if ($property === 'float') {
            return in_array(strtolower($value), ['left', 'right', 'none'], true);
        }

        if ($property === 'display') {
            return in_array(strtolower($value), ['block', 'inline-block', 'table', 'table-cell'], true);
        }

        if ($property === 'vertical-align') {
            return in_array(strtolower($value), ['top', 'middle', 'bottom'], true);
        }

        if ($property === 'line-height') {
            return preg_match('/^\d+(\.\d+)?(px|em|rem|%)?$/i', trim($value)) === 1;
        }

        if ($property === 'font-size') {
            return self::isSafeCssSize($value);
        }

        if ($property === 'border') {
            return preg_match('/^\d+(\.\d+)?px\s+(solid|dashed|dotted)\s+([#a-z0-9(),.\s%-]+)$/i', trim($value)) === 1;
        }

        if ($property === 'border-width') {
            return self::isSafeCssSpacing($value);
        }

        if ($property === 'border-style') {
            return preg_match('/^(solid|dashed|dotted|double|none)$/i', trim($value)) === 1;
        }

        return false;
    }

    private static function sanitizeClassNames(string $classNames): ?string
    {
        $tokens = preg_split('/\s+/', trim($classNames)) ?: [];
        $allowed = [];

        foreach ($tokens as $token) {
            if ($token === '') {
                continue;
            }

            if (preg_match('/^(se-|__se__|sun-editor-)[a-z0-9_-]+$/i', $token)) {
                $allowed[] = $token;
            }
        }

        return $allowed !== [] ? implode(' ', array_unique($allowed)) : null;
    }

    private static function isSafeCssColor(string $value): bool
    {
        $value = trim($value);

        if ($value === '') {
            return false;
        }

        if (preg_match('/^#[0-9a-f]{3}([0-9a-f]{3})?$/i', $value)) {
            return true;
        }

        if (preg_match('/^rgb(a)?\(\s*\d{1,3}\s*(,\s*\d{1,3}\s*){2}(,\s*(0|1|0?\.\d+)\s*)?\)$/i', $value)) {
            return true;
        }

        return preg_match('/^[a-z]{3,20}$/i', $value) === 1;
    }

    private static function isSafeCssSize(string $value): bool
    {
        $value = trim($value);

        if (in_array(strtolower($value), ['auto', 'inherit'], true)) {
            return true;
        }

        return preg_match('/^\d+(\.\d+)?(px|%|em|rem|vh|vw)$/i', $value) === 1;
    }

    private static function isSafeCssSpacing(string $value): bool
    {
        $value = trim($value);

        return preg_match('/^-?\d+(\.\d+)?(px|%|em|rem)(\s+-?\d+(\.\d+)?(px|%|em|rem)){0,3}$/i', $value) === 1;
    }

    private static function sanitizeHref(string $href): ?string
    {
        $href = trim($href);
        if ($href === '') {
            return null;
        }

        $decoded = strtolower(html_entity_decode($href, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));

        if (
            str_starts_with($decoded, 'javascript:') ||
            str_starts_with($decoded, 'data:') ||
            str_starts_with($decoded, 'vbscript:')
        ) {
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

    private static function sanitizeSrc(string $src): ?string
    {
        $src = trim($src);
        if ($src === '') {
            return null;
        }

        $decoded = strtolower(html_entity_decode($src, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));

        if (
            str_starts_with($decoded, 'javascript:') ||
            str_starts_with($decoded, 'data:') ||
            str_starts_with($decoded, 'vbscript:')
        ) {
            return null;
        }

        if (
            str_starts_with($src, '/') ||
            preg_match('/^https?:\/\//i', $src)
        ) {
            return $src;
        }

        return null;
    }
}
