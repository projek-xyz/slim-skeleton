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
    public function __construct($name = 'slim-config', array $settings = [])
    {
        $this->name = $name;
        $this->monolog = new Monolog($this->name);
        $this->settings = array_merge($this->settings, $settings);
        $levels = array_keys(Monolog::getLevels());
        if (!in_array(strtoupper($this->settings['level']), $levels)) {
            $this->settings['level'] = Monolog::DEBUG;
        }

        if (null === $this->settings['directory']) {
            $this->settings['directory'] = directory('storage.logs');
        }

        if ($directory = $this->settings['directory'] && getenv('APP_ENV') !== 'testing') {
            if ($directory === 'syslog') {
                $this->useSyslog($this->settings['level']);
            } elseif (is_dir($directory)) {
                $this->useFiles($this->settings['level']);
            }
        } else {
            $this->monolog->pushHandler(
                new Handler\NullHandler($this->settings['level'])
            );
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
     */
    public function useSyslog($level = Monolog::DEBUG)
    {
        $this->monolog->pushHandler(new Handler\SyslogHandler($this->name, LOG_USER, $level));
    }

    /**
     * Register a file log handler.
     *
     * @param  string $level
     * @param  bool $rotate
     * @return void
     */
    public function useFiles($level = Monolog::DEBUG, $rotate = null)
    {
        $rotate = is_bool($rotate) ? $rotate : $this->settings['rotate'];
        $filepath = $this->settings['directory'].$this->name.'.log';
        $handler = $rotate
            ? new Handler\RotatingFileHandler($filepath, 5, $level)
            : new Handler\StreamHandler($filepath, $level);

        $this->monolog->pushHandler($handler);

        $handler->setFormatter($this->getDefaultFormatter());
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
     * Get a defaut Monolog formatter instance.
     *
     * @return \Monolog\Formatter\LineFormatter
     */
    protected function getDefaultFormatter()
    {
        return new LineFormatter(null, null, true, true);
    }
}
