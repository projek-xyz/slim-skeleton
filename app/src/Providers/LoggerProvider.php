<?php
namespace App\Providers;

use Monolog\Logger;
use Monolog\Handler;
use Monolog\Formatter\LineFormatter;
use Psr\Log\LoggerTrait;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use InvalidArgumentException;
use DateTimeZone;

class LoggerProvider implements ServiceProviderInterface
{
    use LoggerTrait;

    /**
     * Logger basename
     *
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $settings = [
        'directory' => null,
        'filename' => null,
        'timezone' => null,
        'level' => 'DEBUG',
        'handlers' => [],
    ];

    /**
     * @var \Monolog\Logger
     */
    private $monolog;

    /**
     * Register this monolog provider with a Pimple container
     *
     * @param \Pimple\Container $container
     */
    public function register(Container $container)
    {
        $settings = $container->get('settings');

        if (!isset($settings['logger'])) {
            throw new InvalidArgumentException('Logger configuration not found');
        }

        $this->name = $settings['basename'];
        $this->monolog = new Logger($this->name);
        $this->settings = array_merge($this->settings, $settings['logger']);

        // Set Logger Timezone
        if (isset($settings['timezone']) && !empty($settings['timezone'])) {
            if (is_string($settings['timezone'])) {
                $settings['timezone'] = new DateTimeZone($settings['timezone']);
            }
            Logger::setTimezone($settings['timezone']);
        }

        $this->monolog->setHandlers($this->settings['handlers']);

        if (!in_array(strtoupper($this->settings['level']), array_keys(Logger::getLevels()))) {
            $this->settings['level'] = 'DEBUG';
        }

        if ($path = $this->settings['directory']) {
            if ($path === 'syslog') {
                $this->useSyslog($this->settings['level']);
            } elseif (is_dir($path)) {
                $path .= '/'.strtolower($this->name);
                $this->useRotatingFiles($this->settings['level'], $path);
            }
        }

        $container['logger'] = $this;
    }

    /**
     * Pushes a handler on to the stack.
     *
     * @param  \Monolog\Handler\HandlerInterface $handler
     * @return \Monolog\Logger
     */
    public function pushHandler(Handler\HandlerInterface $handler)
    {
        return $this->monolog->pushHandler($handler);
    }

    /**
     * Pops a handler from the stack
     *
     * @return \Monolog\Handler\HandlerInterface
     */
    public function popHandler()
    {
        return $this->monolog->popHandler();
    }

    /**
     * Adds a processor on to the stack.
     *
     * @param  \Callable $callback
     * @return \Monolog\Logger
     */
    public function pushProcessor($callback)
    {
        return $this->monolog->pushProcessor($callback);
    }

    /**
     * Removes the processor on top of the stack and returns it.
     *
     * @return callback
     */
    public function popProcessor()
    {
        return $this->monolog->popProcessor();
    }

    /**
     * Adds a log record.
     *
     * @param  integer $level   The logging level
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return bool    Whether the record has been processed
     */
    public function log($level, $message, array $context = [])
    {
        return $this->monolog->log($level, $message, $context);
    }

    /**
     * Register a Syslog handler.
     *
     * @param  string $level
     * @param  string $name
     * @return void
     */
    public function useSyslog($level = 'debug', $name = null)
    {
        $name || $name = $this->name;
        $this->monolog->pushHandler(
            new Handler\SyslogHandler($name, LOG_USER, $level)
        );

        return $this;
    }

    /**
     * Register an error_log handler.
     *
     * @param  string $level
     * @param  int    $messageType
     * @return void
     */
    public function useErrorLog($level = 'debug', $messageType = Handler\ErrorLogHandler::OPERATING_SYSTEM)
    {
        $this->monolog->pushHandler(
            $handler = new Handler\ErrorLogHandler($messageType, Logger::toMonologLevel($level))
        );
        $handler->setFormatter($this->getDefaultFormatter());

        return $this;
    }

    /**
     * Register a file log handler.
     *
     * @param  string  $level
     * @param  string  $path
     * @return void
     */
    public function useFiles($level = 'debug', $path = null)
    {
        $path || $path = $this->settings['directory'];
        $this->monolog->pushHandler(
            $handler = new Handler\StreamHandler($path, Logger::toMonologLevel($level))
        );
        $handler->setFormatter($this->getDefaultFormatter());

        return $this;
    }

    /**
     * Register a rotating file log handler.
     *
     * @param  string  $level
     * @param  string  $path
     * @return void
     */
    public function useRotatingFiles($level = 'debug', $path = null)
    {
        $path || $path = $this->settings['directory'];
        $this->monolog->pushHandler(
            $handler = new Handler\RotatingFileHandler($path, 5, Logger::toMonologLevel($level))
        );
        $handler->setFormatter($this->getDefaultFormatter());

        return $this;
    }

    /**
     * Get a defaut Monolog formatter instance.
     *
     * @return \Monolog\Formatter\LineFormatter
     */
    protected function getDefaultFormatter()
    {
        return new LineFormatter(null, null, true, true);
    }
}
