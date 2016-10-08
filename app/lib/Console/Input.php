<?php
namespace Projek\Slim\Console;

class Input extends IO
{

    /**
     * CLImate inputs
     */

    /**
     * Wanna ask something
     *
     * @param  string         $prompt     The question you want to ask for
     * @param  string         $default    Default answer
     * @param  array|callable $acceptable Acceptable answer
     * @param  bool           $strict     Case-sensitife?
     * @return string
     */
    public function input($prompt, $default = '', $acceptable = null, $strict = false)
    {
        if ($this->hasSttyAvailable()) {
            $input = $this->climate->input($prompt);

            if (! empty($default)) {
                $input->defaultTo($default);
            }

            if (null !== $acceptable) {
                $input->accept($acceptable, true);
            }

            if (true === $strict) {
                $input->strict();
            }

            return $input->prompt();
        }
        return $default;
    }

    /**
     * Ask something secretly?
     *
     * @param  string $prompt The question you want to ask for
     * @return string
     */
    public function password($prompt)
    {
        if ($this->hasSttyAvailable()) {
            $password = $this->climate->password($prompt);

            return $password->prompt();
        }
        return '';
    }

    /**
     * Choise between yes or no?
     *
     * @param  string $prompt The question you want to ask for
     * @return bool
     */
    public function confirm($prompt)
    {
        if ($this->hasSttyAvailable()) {
            $confirm = $this->climate->confirm($prompt);

            return $confirm->confirmed();
        }
        return '';
    }

    /**
     * Choise multiple answer from given options?
     *
     * @param  string $prompt  The question you want to ask for
     * @param  array  $options Available options
     * @return string
     */
    public function checkboxes($prompt, array $options)
    {
        if ($this->hasSttyAvailable()) {
            $checkboxes = $this->climate->checkboxes($prompt, $options);

            return $checkboxes->prompt();
        }
        return '';
    }

    /**
     * Choise an answer from given options?
     *
     * @param  string $prompt  The question you want to ask for
     * @param  array  $options Available options
     * @return string
     */
    public function radio($prompt, array $options)
    {
        if ($this->hasSttyAvailable()) {
            $radio = $this->climate->radio($prompt, $options);

            return $radio->prompt();
        }
        return '';
    }
}
