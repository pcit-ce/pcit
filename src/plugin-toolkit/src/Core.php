<?php

declare(strict_types=1);

namespace PCIT\Plugin\Toolkit;

class Core
{
    /**
     * Gets whether Actions Step Debug is on or not.
     */
    public function isDebug(): bool
    {
        return 'true' === getenv('PCIT_STEP_DEBUG');
    }

    /**
     * Writes debug message to user log.
     *
     * @param string $message debug message
     */
    public function debug(string $message): void
    {
        echo "::debug::$message".PHP_EOL;
    }

    /**
     * Adds an warning issue.
     *
     * @param string $message warning issue message
     */
    public function warning(string $message): void
    {
        echo "::warning::$message".PHP_EOL;
    }

    /**
     * Adds an error issue.
     *
     * @param string $message error issue message
     */
    public function error(string $message): void
    {
        echo "::error::$message".PHP_EOL;
    }

    /**
     * Sets env variable for this action and future actions in the job.
     *
     * @param string $name  the name of the variable to set
     * @param string $value the value of the variable
     */
    public function exportVariable(string $name, string $value): void
    {
        echo "::set-env name=$name::$value".PHP_EOL;
    }

    /**
     * Registers a secret which will get masked from logs.
     *
     * @param string $secret value of the secret
     */
    public function setSecret(string $secret): void
    {
        echo "::add-mask::$secret".PHP_EOL;
    }

    /**
     * Prepends inputPath to the PATH (for this action and future actions).
     */
    public function addPath(string $inputPath): void
    {
        echo "::add-path::${inputPath}".PHP_EOL;
    }

    /**
     * Gets the value of an input.  The value is also trimmed.
     *
     * @param string $name name of the input to get
     * @returns   string
     */
    public function getInput(string $name, bool $required = false): string
    {
        $value = getenv(strtoupper('INPUT_'.$name)) || '';

        if ($required && !$value) {
            throw new \Exception("Input required and not supplied: ${name}");
        }

        return trim($value);
    }

    /**
     * Sets the value of an output.
     *
     * @param string $name  name of the output to set
     * @param string $value value to store
     */
    public function setOutput(string $name, string $value): void
    {
        echo "::set-output name=$name::$value".PHP_EOL;
    }

    /**
     * Sets the action status to failed.
     * When the action exits it will be with an exit code of 1.
     *
     * @param string $message add error issue message
     */
    public function setFailed(string $message): void
    {
        die($message);
    }

    /**
     * Saves state for current action, the state can only be retrieved by this action's post job execution.
     *
     * @param string $name  name of the state to store
     * @param string $value value to store
     */
    public function saveState(string $name, string $value): void
    {
        echo "::save-state name=$name::$value".PHP_EOL;
    }

    /**
     * Gets the value of an state set by this action's main execution.
     *
     * @param string $name name of the state to get
     */
    public function getState(string $name): string
    {
        return getenv("STATE_${name}") || '';
    }
}
