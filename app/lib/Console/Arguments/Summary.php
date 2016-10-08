<?php
namespace Projek\Slim\Console\Arguments;

use League\CLImate\Argument\Argument;
use League\CLImate\Argument\Summary as BaseSummary;

class Summary extends BaseSummary
{
    /**
     * {@inheritdoc}
     */
    public function output()
    {
        // Print the description if it's defined.
        if ($this->description) {
            $this->climate->out($this->description)->br();
        }

        // Print the usage statement with the arguments without a prefix at the end.
        $this->climate->out('<yellow>Usage</yellow>:');
        $this->climate->tab()->out(sprintf('%s [option]', $this->command));

        // Print argument details.
        foreach (['required', 'optional'] as $type) {
            $this->outputArguments($this->filter->{$type}(), $type);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function outputArguments($arguments, $type)
    {
        if (count($arguments) == 0) {
            return;
        }

        $this->climate->br()->out(
            sprintf('<yellow>%s Arguments</yellow>:', ucwords($type))
        );

        $names = array_map(function ($argument) {
            return strlen($this->argument($argument));
        }, $arguments);

        /** @var  Argument $argument */
        foreach ($arguments as $argument) {
            $str = sprintf(
                '<green>%s</green>%s',
                $arg = $this->argument($argument),
                str_repeat(' ', (max($names) + 2) - strlen($arg))
            );

            if ($argument->description()) {
                $str .= $argument->description();
            }

            $this->climate->tab()->out($str);
        }
    }

    public function argument(Argument $argument)
    {
        $summary = $this->prefixedArguments($argument);

        // Print the argument name if it's not printed yet.
        if (!$argument->noValue()) {
            $summary .= sprintf('[=%s]', strtoupper($argument->name()));
        }

        if ($argument->defaultValue()) {
            $summary .= ' (default: '.$argument->defaultValue().')';
        }

        return $summary;
    }

    protected function prefixedArguments(Argument $argument)
    {
        $prefixes = [$argument->prefix(), $argument->longPrefix()];
        $summary  = [];

        foreach ($prefixes as $key => $prefix) {
            if (!$prefix) {
                continue;
            }

            $sub = str_repeat('-', $key + 1) . $prefix;
            $summary[] = $sub;
        }

        if ($argument->prefix()) {
            return implode(', ', $summary);
        }

        return '    '.implode('', $summary);
    }
}
