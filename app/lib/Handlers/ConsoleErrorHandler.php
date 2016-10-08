<?php
namespace Projek\Slim\Handlers;

use Projek\Slim\Console\Output;
use Psr\Log\LogLevel;

class ConsoleErrorHandler
{
    /**
     * @var  Output
     */
    protected $output;

    /**
     * @var  bool
     */
    protected $displayErrorDetails = false;

    /**
     * @param  Output $output
     * @param  bool $displayErrorDetails
     */
    public function __construct(Output $output, $displayErrorDetails)
    {
        $this->output = $output;
        $this->displayErrorDetails = $displayErrorDetails;
    }

    public function __invoke(\Exception $exception)
    {
        $this->renderExeption($exception);

        while ($exception = $exception->getPrevious()) {
            $this->output->br()->out('<bold>Previous exception</bold>');
            $this->renderExeption($exception);
        }
    }

    protected function renderExeption(\Exception $exception)
    {
        $this->output->out(
            sprintf('<background_red><underline><bold>Error: %s</bold></underline></background_red>', $type = get_class($exception))
        );

        $message = $exception->getMessage();
        if ($code = $exception->getCode()) {
            $message = sprintf('[%s] %s', $code, $message);
        }

        $this->output->tab()->out(sprintf('<bold>%s</bold>', $message));

        if ($file = $exception->getFile()) {
            $file = str_replace(ROOT_DIR, DIRECTORY_SEPARATOR, $file);
            if ($code = $exception->getLine()) {
                $file = sprintf('%s(%s)', $file, $code);
            }

            $this->output->tab()->out(sprintf('<bold>%s</bold>', $file));
        }

        if ($this->displayErrorDetails && $traces = $exception->getTraceAsString()) {
            $this->output->br()->out('<bold><underline>Trace:</underline></bold>');
            $this->output->out(str_replace(ROOT_DIR, '/', $traces));
        }

        logger(LogLevel::ERROR, $message.' on '.$file, ['trace' => $traces]);
    }
}
