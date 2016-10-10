<?php
namespace Projek\Slim\Console\Extensions;

use League\CLImate\TerminalObject\Basic\Tab as BasicTab;

class Tab extends BasicTab
{
    /**
     * {@inheritdoc}
     */
    public function result()
    {
        return str_repeat('  ', $this->count);
    }
}
