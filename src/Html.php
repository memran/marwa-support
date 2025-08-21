<?php
declare(strict_types=1);

namespace Marwa\Support;

use DOMDocument;
use DOMElement;
use DOMXPath;
use InvalidArgumentException;
use RuntimeException;

class Html
{
    /**
     * Generate HTML element with attributes
     */
    public static function element(string $tag, array $attributes = [], ?string $content = null): string
    {
        $tag = strtolower(trim($tag));
        self::validateTag($tag);
        
        $attrs = self::attributes($attributes);
        
        if (self::isVoidElement($tag)) {
            return "<{$tag}{$attrs}>";
        }
        
        return "<{$tag}{$attrs}>" . ($content ?? '') . "</{$tag}>";
    }

    /**
     * Generate HTML attributes string
     */
    public static function attributes(array $attributes): string
    {
        $html = [];
        
        foreach ($attributes as $key => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $html[] = $key;
                }
            } elseif (is_array($value)) {
                $html[] = $key . '="' . implode(' ', array_map('htmlspecialchars', $value)) . '"';
            } elseif ($value !== null) {
                $html[] = $key . '="' . htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') . '"';
            }
        }
        
        return $html ? ' ' . implode(' ', $html) : '';
    }

    /**
     * Generate link tag
     */
    public static function link(string $url, ?string $text = null, array $attributes = []): string
    {
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
        $attributes['src'] = $src;
        $attributes['alt'] = $alt;
        
        return self::element('img', $attributes);
    }

    /**
     * Generate script tag
     */
    public static function script(string $src, array $attributes = []): string
    {
        $attributes['src'] = $src;
        
        return self::element('script', $attributes);
    }

    /**
     * Generate style/link tag
     */
    public static function style(string $href, array $attributes = []): string
    {
        $attributes['rel'] = 'stylesheet';
        $attributes['href'] = $href;
        
        return self::element('link', $attributes);
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
            $optionsHtml .= self::element('option', $optionAttrs, $label);
        }
        
        return self::element('select', $attributes, $optionsHtml);
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
                
                $html .= self::element($tag, $attributes, $children);
            } else {
                $html .= self::element($tag, [], $content);
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
        // Basic CSS to XPath conversion
        $selector = preg_replace('/\s*>\s*/', '/', $selector);
        $selector = preg_replace('/\s*\+\s*/', '/following-sibling::*[1]/', $selector);
        $selector = preg_replace('/\s*~\s*/', '/following-sibling::', $selector);
        $selector = preg_replace('/#([\w-]+)/', '[@id="$1"]', $selector);
        $selector = preg_replace('/\.([\w-]+)/', '[contains(concat(" ", @class, " "), " $1 ")]', $selector);
        
        return '//' . $selector;
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
            'em', 'embed',
            'fieldset', 'figcaption', 'figure', 'footer', 'form',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'header', 'hr', 'html',
            'i', 'iframe', 'img', 'input', 'ins',
            'kbd',
            'label', 'legend', 'li', 'link',
            'main', 'map', 'mark', 'meta', 'meter',
            'nav', 'noscript',
            'object', 'ol', 'optgroup', 'option',
            'p', 'param', 'picture', 'pre', 'progress',
            'q',
            'rp', 'rt', 'ruby',
            's', 'samp', 'script', 'section', 'select', 'small', 'source', 'span', 'strong', 'style', 'sub', 'summary', 'sup',
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

        return self::doctype() . 
               self::element('html', ['lang' => 'en'], 
                   self::element('head', [],
                       self::element('title', [], $title) .
                       $metaTags .
                       self::element('meta', ['charset' => 'UTF-8']) .
                       self::element('meta', ['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0'])
                   ) .
                   self::element('body', [], $content)
               );
    }
}