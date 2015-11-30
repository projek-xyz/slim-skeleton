<?php
namespace App\Providers;

use Negotiation;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Http\Message\ServerRequestInterface;

class NegotiatorProvider implements ServiceProviderInterface
{
    /**
     * @var string Default format
     */
    protected $default = [
        'format' => 'html',
        'language' => 'en',
    ];

    /**
     * @var array Available formats with the mime types
     */
    protected $formats = [
        'html'  => ['text/html', 'application/xhtml+xml'],
        'css'   => ['text/css'],
        'gif'   => ['image/gif'],
        'png'   => ['image/png', 'image/x-png'],
        'jpg'   => ['image/jpeg', 'image/jpg'],
        'jpeg'  => ['image/jpeg', 'image/jpg'],
        'json'  => ['application/json', 'text/json', 'application/x-json'],
        'jsonp' => ['text/javascript', 'application/javascript', 'application/x-javascript'],
        'js'    => ['text/javascript', 'application/javascript', 'application/x-javascript'],
        'pdf'   => ['application/pdf', 'application/x-download'],
        'rdf'   => ['application/rdf+xml'],
        'rss'   => ['application/rss+xml'],
        'atom'  => ['application/atom+xml'],
        'xml'   => ['text/xml', 'application/xml', 'application/x-xml'],
        'txt'   => ['text/plain'],
        'mp4'   => ['video/mp4'],
        'ogg'   => ['audio/ogg'],
        'ogv'   => ['video/ogg'],
        'webm'  => ['video/webm'],
        'webp'  => ['image/webp'],
        'svg'   => ['image/svg+xml'],
        'zip'   => ['application/zip', 'application/x-zip', 'application/x-zip-compressed'],
    ];

    /**
     * Register this content negotiator provider with a Pimple container
     *
     * @param \Pimple\Container $container
     */
    public function register(Container $container)
    {
        $settings = $container->get('settings');

        // Overwrite default language based on setting.
        $this->default['language'] = $settings['lang']['default'];

        $container['negotiator'] = $this;
    }

    /**
     * Get content request format
     *
     * @param  Psr\Http\Message\ServerRequestInterface $request
     * @return string
     */
    public function getFormat(ServerRequestInterface $request)
    {
        $format = strtolower(pathinfo($request->getUri()->getPath(), PATHINFO_EXTENSION));

        if (isset($this->formats[$format])) {
            return $format;
        }

        $accept = $request->getHeaderLine('Accept');
        if (empty($accept)) {
            return $this->default['format'];
        }

        $priorities = call_user_func_array('array_merge', array_values($this->formats));

        try {
            $accept = (new Negotiation\Negotiator())->getBest($accept, $priorities);
        } catch (\Exception $exception) {
            return $this->default['format'];
        }

        if ($accept) {
            $accept = $accept->getValue();

            foreach ($this->formats as $extension => $headers) {
                if (in_array($accept, $headers)) {
                    return $extension;
                }
            }
        }

        return $this->default['format'];
    }

    /**
     * Get content language
     *
     * @param  Psr\Http\Message\ServerRequestInterface $request
     * @return string
     */
    public function getLanguage(ServerRequestInterface $request)
    {
        $accept = $request->getHeaderLine('Accept-Language');
        $priorities = ['id', 'en'];

        try {
            $accept = (new Negotiation\LanguageNegotiator())->getBest($accept, $priorities);
        } catch (\Exception $exception) {
            return $this->default['language'];
        }

        if ($accept) {
            return $accept->getValue();
        }

        return $this->default['language'];
    }
}
