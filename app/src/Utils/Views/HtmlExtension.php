<?php
namespace App\Utils\Views;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class HtmlExtension implements ExtensionInterface
{

    /**
     * @var \League\Plates\Template\Template
     */
    // public $template;

    /**
     * Register extension function.
     *
     * @return null
     */
    public function register(Engine $engine)
    {
        $engine->registerFunction('js', [$this, 'js']);
        $engine->registerFunction('css', [$this, 'css']);
        $engine->registerFunction('link', [$this, 'link']);
        $engine->registerFunction('htmlAttr', [$this, 'htmlAttr']);
    }

    /**
     * Retrieve JS script tag.
     *
     * @param  string $path  Js Path
     * @param  bool   $async Is async
     * @return string
     */
    public function js($path, $async = false)
    {
        $attrs = [
            'src'  => $this->template->baseUrl($path),
        ];

        if ($async === true) {
            $attrs[] = 'async';
        }

        return '<script '.$this->htmlAttr($attrs).'></script>';
    }

    /**
     * Retrieve CSS link tag.
     *
     * @param  string $path  Js Path
     * @return string
     */
    public function css($href)
    {
        return $this->link($href, 'stylesheet');
    }

    /**
     * Retrieve link tag.
     *
     * @param  string $href href attribute value
     * @param  string $rel  rel type attribute
     * @return string
     */
    public function link($href, $rel = '')
    {
        $attrs = [
            'href' => $this->template->baseUrl($href),
        ];

        if ($rel = $this->validateRelAttribute($rel)) {
            $attrs['rel'] = $this->escape($rel);
        }

        return '<link '.$this->htmlAttr($attrs).'>';
    }

    /**
     * Parse html attributes
     *
     * @param  string|array(string) $attributes Html Attributes
     * @return string
     */
    public function htmlAttr($attributes = null)
    {
        if (empty($attributes)) {
            return '';
        }

        if (is_string($attributes)) {
            return $attributes;
        }

        if (is_object($attributes)) {
            $attributes = (array) $attributes;
        }

        if (is_array($attributes)) {
            $attrs = [];
            foreach ($attributes as $key => $val) {
                if (is_int($key)) {
                    $attrs[] = $val;
                } else {
                    $attrs[] = $key.'="'.$this->escape($val).'"';
                }
            }

            return implode(' ', $attrs);
        }

        return false;
    }

    /**
     * Validate rel attribute
     *
     * @param  string|array(string) $rel Rel attribute
     * @return string
     */
    protected function validateRelAttribute($rel)
    {
        $validRels = [
            'alternate', 'archives', 'author', 'bookmark', 'external',
            'first', 'next', 'prev', 'last', 'up',
            'license', 'nofollow', 'noreferrer', 'pingback', 'prefetch',
            'search', 'help', 'icon', 'sidebar', 'stylesheet', 'tag'
        ];

        if (in_array($rel, $validRels)) {
            if (is_array($rel)) {
                return implode(' ', $rel);
            }
            return $rel;
        }
        return false;
    }

    private function escape($string)
    {
        if (is_callable($this->template, 'escape')) {
            return $this->template->escape($string);
        }

        $espace = new \ReflectionMethod($this->template, 'escape');
        $espace->setAccessible(true);

        return $espace->invoke($this->template, $string);
    }
}
