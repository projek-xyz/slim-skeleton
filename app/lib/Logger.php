<?php
namespace Projek\Slim;

use Monolog\Logger as Monolog;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler;
use Psr\Log\LoggerTrait;

class Logger
{
    use LoggerTrait;

    /**
     * Logger name
     *
     * @var string
     */
    private $name = 'slim-config';

    /**
     * Logger settings
     *
     * @var array
     */
    private $settings = [
        'directory' => null,
        'rotate' => false,
        'level' => Monolog::DEBUG,
    ];

    /**
     * Monolog instance
     *
     * @var \Monolog\Logger
     */
    private $monolog;

    /**
     * Class constructor
     *
     * @param string $name     Logger name
     * @param array  $settings Logger settings
     */
    public function __construct($name = 'slim-config', $settings = [])
    {
        $this->name = $name;
        $this->monolog = new Monolog($this->name);
        $this->settings = array_merge($this->settings, $settings);

        if ($timezone = setting('timezone')) {
            if (is_string($timezone)) {
                $timezone = new \DateTimeZone($timezone);
            }
            Monolog::setTimezone($timezone);
        }

        $levels = array_keys(Monolog::getLevels());
        if (!in_array(strtoupper($this->settings['level']), $levels)) {
            $this->settings['level'] = Monolog::DEBUG;
        }

        if ($path = $this->settings['directory']) {
            if ($path === 'syslog') {
                $this->useSyslog($this->settings['level'], $this->name);
            } elseif (is_dir($path)) {
                $path .= '/'.$this->name.'.log';

                if ($this->settings['rotate']) {
                    $this->useRotatingFiles($this->settings['level'], $path);
                } else {
                    $this->useFiles($this->settings['level'], $path);
                }
            }
        }
    }

    /**
     * Returns Monolog Instance
     *
     * @return \Monolog\Logger
     */
    public function getMonolog()
    {
        return $this->monolog;
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
     * @param  callable $callback
     * @return \Monolog\Logger
     */
    public function pushProcessor($callback)
    {
        return $this->monolog->pushProcessor($callback);
    }

    /**
     * Removes the processor on top of the stack and returns it.
     *
     * @return callable
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
     * Alias for $this->log()
     *
     * @param  int $level
     * @param  string $message
     * @param  array $context
     * @return bool
     */
    public function __invoke($level, $message, array $context = [])
    {
        return $this->monolog->log($level, $message, $context);
    }

    /**
     * Register a Syslog handler.
     *
     * @param  string $level
     * @param  string $name
     */
    public function useSyslog($level = Monolog::DEBUG, $name = null)
    {
        $name || $name = $this->name;
        $this->monolog->pushHandler(new Handler\SyslogHandler($name, LOG_USER, $level));
    }

    /**
     * Register an error_log handler.
     *
     * @param  string $level
     * @param  int    $messageType
     */
    public function useErrorLog($level = Monolog::DEBUG, $messageType = Handler\ErrorLogHandler::OPERATING_SYSTEM)
    {
        $this->monolog->pushHandler(
            $handler = new Handler\ErrorLogHandler($messageType, $level)
        );
        $handler->setFormatter($this->getDefaultFormatter());
    }

    /**
     * Register a file log handler.
     *
     * @param  string $level
     * @param  string $filename
     * @return void
     */
    public function useFiles($level = Monolog::DEBUG, $filename = null)
    {
        $this->monolog->pushHandler(
            $handler = new Handler\StreamHandler($filename, $level)
        );
        $handler->setFormatter($this->getDefaultFormatter());
    }

    /**
     * Register a rotating file log handler.
     *
     * @param  string $level
     * @param  string $filename
     */
    public function useRotatingFiles($level = Monolog::DEBUG, $filename = null)
    {
        $this->monolog->pushHandler(
            $handler = new Handler\RotatingFileHandler($filename, 5, $level)
        );
        $handler->setFormatter($this->getDefaultFormatter());
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
