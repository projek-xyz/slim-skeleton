<?php
namespace Projek\Slim\Console;

class Output extends IO
{
    /**
     * Returns CLImate comment output
     *
     * @param  string $string Output
     * @return mixed
     */
    public function comment($string)
    {
        return $this->climate->comment($string);
    }

    /**
     * Returns CLImate whisper output
     *
     * @param  string $string Output
     * @return mixed
     */
    public function whisper($string)
    {
        return $this->climate->whisper($string);
    }

    /**
     * Returns CLImate shout output
     *
     * @param  string $string Output
     * @return mixed
     */
    public function shout($string)
    {
        return $this->climate->shout($string);
    }

    /**
     * Returns CLImate error output
     *
     * @param  string $string Output
     * @return mixed
     */
    public function error($string)
    {
        return $this->climate->error($string);
    }

    /**
     * CLImate base output
     */

    /**
     * Returns CLImate output
     *
     * @param  string $string Output
     * @return mixed
     */
    public function out($string)
    {
        return $this->climate->out($string);
    }

    /**
     * Returns CLImate inline text
     *
     * @param  string $string Output
     * @return mixed
     */
    public function inline($string)
    {
        return $this->climate->inline($string);
    }

    /**
     * Returns CLImate draw art
     * @see http://climate.thephpleague.com/terminal-objects/draw/
     *
     * @param  string $string Output
     * @return mixed
     */
    public function draw($string)
    {
        return $this->climate->draw($string);
    }

    /**
     * Returns CLImate json
     * @see http://climate.thephpleague.com/terminal-objects/json/
     *
     * @param  mixed $mixed String|Array|Object
     * @return mixed
     */
    public function json($mixed)
    {
        return $this->climate->json($mixed);
    }

    /**
     * Returns CLImate table
     * @see http://climate.thephpleague.com/terminal-objects/table/
     *
     * @param  array $array Table data
     * @return mixed
     */
    public function table(array $array)
    {
        return $this->climate->table($array);
    }

    /**
     * Draw a border
     * @see http://climate.thephpleague.com/terminal-objects/border/
     *
     * @param  string $char   Border character
     * @param  int    $length Border length
     * @return mixed
     */
    public function border($char = null, $length = null)
    {
        return $this->climate->border($char, $length);
    }

    /**
     * Draw padding
     * @see http://climate.thephpleague.com/terminal-objects/padding/
     *
     * @param  int    $length Padding length
     * @param  string $char   Padding character
     * @return mixed
     */
    public function padding($length = 0, $char = '.')
    {
        return $this->climate->padding($length, $char);
    }

    /**
     * Returns output in columns
     * @see http://climate.thephpleague.com/terminal-objects/columns/
     *
     * @param  array $data         Output data
     * @param  int   $column_count Number of columns
     * @return mixed
     */
    public function columns(array $data, $column_count = null)
    {
        return $this->climate->columns($data, $column_count);
    }

    /**
     * Pay attantion to this output
     * @see http://climate.thephpleague.com/terminal-objects/flank/
     *
     * @param  string $output Output string
     * @param  string $char   Special character
     * @param  int    $length Character length
     * @return mixed
     */
    public function flank($output, $char = null, $length = null)
    {
        return $this->climate->flank($output, $char, $length);
    }

    /**
     * Create a progressbar
     * @see http://climate.thephpleague.com/terminal-objects/progress-bar/
     *
     * @param  int   $total Total progress
     * @return mixed
     */
    public function progress($total = null)
    {
        if ($this->hasSttyAvailable()) {
            return $this->climate->progress($total);
        }
    }

    /**
     * Dumb any data
     * @see http://climate.thephpleague.com/terminal-objects/dump/
     *
     * @param  mixed $array Data to dump
     * @return mixed
     */
    public function dump($array)
    {
        return $this->climate->dump($array);
    }

    /**
     * Returns CLImate new line
     * @see http://climate.thephpleague.com/terminal-objects/br/
     *
     * @param  int $count Number of new line
     * @return mixed
     */
    public function br($count = 1)
    {
        return $this->climate->br($count);
    }

    /**
     * Returns CLImate new tab
     * @see http://climate.thephpleague.com/terminal-objects/tab/
     *
     * @param  int $count Number of new tab
     * @return mixed
     */
    public function tab($count = 1)
    {
        return $this->climate->tab($count);
    }

    /**
     * Returns CLImate clear output
     * @see http://climate.thephpleague.com/terminal-objects/clear/
     *
     * @return mixed
     */
    public function clear()
    {
        return $this->climate->clear();
    }
}
