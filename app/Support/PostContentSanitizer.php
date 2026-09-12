<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;

final class PostContentSanitizer
{
    private const ALLOWED_TAGS = [
        'a', 'blockquote', 'br', 'em', 'h2', 'h3', 'h4', 'img', 'li', 'ol',
        'p', 'strong', 'u', 'ul', 'video',
    ];

    private const ALLOWED_ATTRIBUTES = [
        'a' => ['href', 'target', 'rel'],
        'img' => ['src', 'alt', 'loading', 'class'],
        'video' => ['src', 'controls', 'preload', 'class'],
        '*' => ['data-temp-media'],
    ];

    public function sanitize(string $html): string
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8">'.'<div id="post-content">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('post-content');
        if (! $root) {
            return '';
        }

        $this->sanitizeChildren($root->childNodes);

        $result = '';
        foreach ($root->childNodes as $child) {
            $result .= $document->saveHTML($child);
        }

        return $result;
    }

    private function sanitizeChildren(DOMNodeList $nodes): void
    {
        for ($index = $nodes->length - 1; $index >= 0; $index--) {
            $node = $nodes->item($index);
            if (! $node) {
                continue;
            }

            if (! $node instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($node->tagName);
            if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                $this->unwrap($node);
                continue;
            }

            $this->sanitizeAttributes($node, $tag);
            $this->sanitizeChildren($node->childNodes);
        }
    }

    private function sanitizeAttributes(DOMElement $element, string $tag): void
    {
        $allowed = array_merge(self::ALLOWED_ATTRIBUTES['*'], self::ALLOWED_ATTRIBUTES[$tag] ?? []);

        foreach (iterator_to_array($element->attributes) as $attribute) {
            $name = strtolower($attribute->name);
            if (! in_array($name, $allowed, true)) {
                $element->removeAttribute($attribute->name);
                continue;
            }

            if (in_array($name, ['src', 'href'], true) && ! $this->isSafeUrl($attribute->value)) {
                $element->removeAttribute($attribute->name);
            }
        }

        if ($tag === 'a' && $element->hasAttribute('href')) {
            $element->setAttribute('rel', 'noopener noreferrer');
        }
    }

    private function isSafeUrl(string $url): bool
    {
        $trimmed = trim($url);
        if ($trimmed === '') {
            return false;
        }

        if (str_starts_with($trimmed, 'data-temp-media=')) {
            return true;
        }

        $scheme = parse_url($trimmed, PHP_URL_SCHEME);
        return $scheme === null || in_array(strtolower($scheme), ['http', 'https'], true);
    }

    private function unwrap(DOMElement $element): void
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
}
