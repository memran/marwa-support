<?php

declare(strict_types=1);

namespace Marwa\Support;

use DOMDocument;
use DOMElement;
use DOMXPath;
use InvalidArgumentException;

/**
 * Class Html
 * Provides methods for generating and manipulating HTML elements.
 */
class Html
{
    /**
     * Generate HTML element with attributes
     *
     * @param string|null $content  Raw HTML content. Only pass unescaped content
     *                              when it has already been escaped or validated.
     * @param bool $escapeContent   Set to false only when $content is trusted
     *                              pre-escaped HTML (e.g. nested element output).
     */
    public static function element(string $tag, array $attributes = [], ?string $content = null, bool $escapeContent = true): string
    {
        $tag = strtolower(trim($tag));
        self::validateTag($tag);

        $attrs = self::attributes($attributes);

        if (self::isVoidElement($tag)) {
            return "<{$tag}{$attrs}>";
        }

        $content = $content ?? '';
        if ($escapeContent) {
            $content = self::escape($content);
        }

        return "<{$tag}{$attrs}>" . $content . "</{$tag}>";
    }

    /**
     * Generate HTML attributes string
     */
    public static function attributes(array $attributes): string
    {
        $html = [];

        foreach ($attributes as $key => $value) {
            self::validateAttributeName((string) $key);

            if (is_bool($value)) {
                if ($value) {
                    $html[] = $key;
                }
            } elseif (is_array($value)) {
                $html[] = $key . '="' . implode(' ', array_map(
                    fn ($item) => self::escapeAttributeValue((string) $key, (string) $item),
                    $value
                )) . '"';
            } elseif ($value !== null) {
                $html[] = $key . '="' . self::escapeAttributeValue((string) $key, (string) $value) . '"';
            }
        }

        return $html ? ' ' . implode(' ', $html) : '';
    }

    /**
     * Generate link tag
     */
    public static function link(string $url, ?string $text = null, array $attributes = []): string
    {
        if (!self::isValidUrl($url)) {
            throw new InvalidArgumentException('Invalid link URL');
        }
        $attributes['href'] = $url;

        if ($text === null) {
            $text = $url;
        }

        return self::element('a', $attributes, $text);
    }

    /**
     * Generate image tag
     */
    public static function image(string $src, string $alt = '', array $attributes = []): string
    {
        if (!self::isValidUrl($src)) {
            throw new InvalidArgumentException('Invalid image src URL');
        }
        $attributes['src'] = $src;
        $attributes['alt'] = $alt;

        return self::element('img', $attributes);
    }

    /**
     * Generate script tag
     */
    public static function script(string $src, array $attributes = []): string
    {
        if (!self::isValidUrl($src)) {
            throw new InvalidArgumentException('Invalid script source URL');
        }
        $attributes['src'] = $src;

        return self::element('script', $attributes);
    }

    /**
     * Generate style/link tag
     */
    public static function style(string $href, array $attributes = []): string
    {
        if (!self::isValidUrl($href)) {
            throw new InvalidArgumentException('Invalid style href URL');
        }
        $attributes['rel'] = 'stylesheet';
        $attributes['href'] = $href;

        return self::element('link', $attributes);
    }

    private static function isValidUrl(string $url): bool
    {
        if (preg_match('/^https?:\/\//i', $url)) {
            return filter_var($url, FILTER_VALIDATE_URL) !== false;
        }

        if (preg_match('/^\//', $url)) {
            return true;
        }

        return false;
    }

    /**
     * Generate meta tag
     */
    public static function meta(string $name, string $content, array $attributes = []): string
    {
        $attributes['name'] = $name;
        $attributes['content'] = $content;

        return self::element('meta', $attributes);
    }

    /**
     * Generate form element
     */
    public static function form(string $action, string $method = 'POST', array $attributes = []): string
    {
        $attributes['action'] = $action;
        $attributes['method'] = strtoupper($method);

        return self::element('form', $attributes);
    }

    /**
     * Generate input field
     */
    public static function input(string $type, string $name, ?string $value = null, array $attributes = []): string
    {
        $attributes['type'] = $type;
        $attributes['name'] = $name;

        if ($value !== null) {
            $attributes['value'] = $value;
        }

        return self::element('input', $attributes);
    }

    /**
     * Generate select dropdown
     */
    public static function select(string $name, array $options, ?string $selected = null, array $attributes = []): string
    {
        $attributes['name'] = $name;

        $optionsHtml = '';
        foreach ($options as $value => $label) {
            $optionAttrs = ['value' => $value];
            if ($selected !== null && (string)$value === (string)$selected) {
                $optionAttrs['selected'] = true;
            }
            $optionsHtml .= self::element('option', $optionAttrs, (string) $label);
        }

        return self::element('select', $attributes, $optionsHtml, false);
    }

    /**
     * Generate HTML from array structure
     */
    public static function fromArray(array $structure): string
    {
        $html = '';

        foreach ($structure as $tag => $content) {
            if (is_array($content)) {
                $attributes = $content['attributes'] ?? [];
                $children = $content['content'] ?? '';

                if (is_array($children)) {
                    $children = self::fromArray($children);
                }

                $html .= self::element($tag, $attributes, (string) $children, false);
            } else {
                $html .= self::element($tag, [], (string) $content);
            }
        }

        return $html;
    }

    /**
     * Minify HTML content
     */
    public static function minify(string $html): string
    {
        $search = [
            '/\>[^\S ]+/s',      // strip whitespace after tags
            '/[^\S ]+\</s',      // strip whitespace before tags
            '/(\s)+/s',          // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/', // remove HTML comments
        ];

        $replace = ['>', '<', '\\1', ''];

        return preg_replace($search, $replace, $html);
    }

    /**
     * Extract text from HTML (strip tags)
     */
    public static function text(string $html): string
    {
        return html_entity_decode(strip_tags($html));
    }

    /**
     * Parse HTML and extract elements by selector
     */
    public static function extract(string $html, string $selector): array
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);

        $xpath = new DOMXPath($dom);
        $elements = $xpath->query(self::cssToXpath($selector));

        $result = [];
        foreach ($elements as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }

            $result[] = [
                'html' => $dom->saveHTML($element),
                'text' => $element->textContent,
                'attributes' => self::getElementAttributes($element)
            ];
        }

        return $result;
    }

    /**
     * Convert CSS selector to XPath
     */
    private static function cssToXpath(string $selector): string
    {
        $selector = preg_replace('/[^a-zA-Z0-9\s>+~.#:\[\]="\',\-\(\)*]/', '', $selector);

        $selector = preg_replace('/\s*>\s*/', '/', $selector);
        $selector = preg_replace('/\s*\+\s*/', '/following-sibling::*[1]/', $selector);
        $selector = preg_replace('/\s*~\s*/', '/following-sibling::', $selector);
        $selector = preg_replace('/#([\w-]+)/', '[@id="$1"]', $selector);
        $selector = preg_replace('/\.([\w-]+)/', '[contains(concat(" ", @class, " "), " $1 ")]', $selector);

        return '//' . ltrim($selector, '/');
    }

    /**
     * Get all attributes from DOM element
     */
    private static function getElementAttributes(DOMElement $element): array
    {
        $attributes = [];
        foreach ($element->attributes as $attr) {
            $attributes[$attr->nodeName] = $attr->nodeValue;
        }
        return $attributes;
    }

    /**
     * Validate HTML tag
     */
    private static function validateTag(string $tag): void
    {
        $validTags = [
            'a', 'abbr', 'address', 'area', 'article', 'aside', 'audio',
            'b', 'base', 'bdi', 'bdo', 'blockquote', 'body', 'br', 'button',
            'canvas', 'caption', 'cite', 'code', 'col', 'colgroup',
            'data', 'datalist', 'dd', 'del', 'details', 'dfn', 'dialog', 'div', 'dl', 'dt',
            'em',
            'fieldset', 'figcaption', 'figure', 'footer', 'form',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'header', 'hr', 'html',
            'i', 'img', 'input', 'ins',
            'kbd',
            'label', 'legend', 'li', 'link',
            'main', 'map', 'mark', 'meta', 'meter',
            'nav', 'noscript',
            'ol', 'optgroup', 'option',
            'p', 'param', 'picture', 'pre', 'progress',
            'q',
            'rp', 'rt', 'ruby',
            's', 'samp', 'section', 'select', 'small', 'source', 'span', 'strong', 'sub', 'summary', 'sup',
            'table', 'tbody', 'td', 'template', 'textarea', 'tfoot', 'th', 'thead', 'time', 'title', 'tr', 'track',
            'u', 'ul',
            'var', 'video',
            'wbr'
        ];

        if (!in_array($tag, $validTags, true)) {
            throw new InvalidArgumentException("Invalid HTML tag: {$tag}");
        }
    }

    /**
     * Check if element is void (self-closing)
     */
    private static function isVoidElement(string $tag): bool
    {
        $voidElements = [
            'area', 'base', 'br', 'col', 'embed', 'hr', 'img',
            'input', 'link', 'meta', 'param', 'source', 'track', 'wbr'
        ];

        return in_array($tag, $voidElements, true);
    }

    /**
     * Escape HTML special characters
     */
    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', true);
    }

    /**
     * Decode HTML entities
     */
    public static function decode(string $value): string
    {
        return html_entity_decode($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate HTML5 doctype
     */
    public static function doctype(): string
    {
        return '<!DOCTYPE html>';
    }

    /**
     * Generate HTML document
     */
    public static function document(string $title, string $content, array $meta = []): string
    {
        $metaTags = '';
        foreach ($meta as $name => $content) {
            $metaTags .= self::meta($name, $content);
        }

        $head = self::element(
            'head',
            [],
            self::element('title', [], $title) .
            $metaTags .
            self::element('meta', ['charset' => 'UTF-8']) .
            self::element('meta', ['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0']),
            false
        );

        return self::doctype() . self::element(
            'html',
            ['lang' => 'en'],
            $head . self::element('body', [], $content),
            false
        );
    }

    /**
     * Generate an HTML element with trusted raw HTML content.
     *
     * WARNING: $content is rendered unescaped. Only pass content that has
     * already been validated or escaped. Never pass user input directly.
     */
    public static function rawElement(string $tag, array $attributes = [], ?string $content = null): string
    {
        return self::element($tag, $attributes, $content, false);
    }

    private static function validateAttributeName(string $name): void
    {
        if (!preg_match('/^[A-Za-z_:][A-Za-z0-9:._-]*$/', $name)) {
            throw new InvalidArgumentException("Invalid HTML attribute: {$name}");
        }

        if (stripos($name, 'on') === 0) {
            throw new InvalidArgumentException("Event handler attributes are not allowed: {$name}");
        }
    }

    private static function escapeAttributeValue(string $name, string $value): string
    {
        if (in_array(strtolower($name), ['href', 'src', 'action', 'formaction', 'poster'], true)) {
            self::validateUrlAttribute($value, $name);
        }

        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private static function validateUrlAttribute(string $value, string $name): void
    {
        if (preg_match('/[\s\x00-\x1F\x7F]/', $value)) {
            throw new InvalidArgumentException("Invalid URL attribute value for {$name}");
        }

        $scheme = parse_url($value, PHP_URL_SCHEME);
        if ($scheme === null || $scheme === false || $scheme === '') {
            return;
        }

        if (!in_array(strtolower($scheme), ['http', 'https', 'mailto', 'tel'], true)) {
            throw new InvalidArgumentException("Unsafe URL scheme for {$name}: {$scheme}");
        }
    }
}
