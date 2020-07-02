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
        $message = $this->escapeData($message);
        echo "::debug::$message"."\n";
    }

    /**
     * Adds an warning issue.
     *
     * @param string $message warning issue message
     */
    public function warning(string $message,
                            string $file = null,
                            int $line = null,
                            int $col = null): void
    {
        $message = $this->escapeData($message);
        if ($file && (null !== $line) && (null !== $col)) {
            echo "::warning file=$file,line=$line,col=$col::$message"."\n";

            return;
        }

        if ($file && (null !== $line)) {
            echo "::warning file=$file,line=$line::$message"."\n";

            return;
        }

        if ($file) {
            echo "::warning file=$file::$message"."\n";

            return;
        }

        echo "::warning::$message"."\n";
    }

    /**
     * Adds an error issue.
     *
     * @param string $message error issue message
     */
    public function error(string $message,
                          string $file = null,
                          int $line = null,
                          int $col = null): void
    {
        $message = $this->escapeData($message);
        if ($file && (null !== $line) && (null !== $col)) {
            echo "::error file=$file,line=$line,col=$col::$message"."\n";

            return;
        }

        if ($file && (null !== $line)) {
            echo "::error file=$file,line=$line::$message"."\n";

            return;
        }

        if ($file) {
            echo "::error file=$file::$message"."\n";

            return;
        }
        echo "::error::$message"."\n";
    }

    /**
     * Sets env variable for this action and future actions in the job.
     *
     * @param string $name  the name of the variable to set
     * @param string $value the value of the variable
     */
    public function exportVariable(string $name, string $value): void
    {
        putenv("$name=$value");

        $value = $this->escapeData($value);
        echo "::set-env name=$name::$value"."\n";
    }

    /**
     * Registers a secret which will get masked from logs.
     *
     * @param string $secret value of the secret
     */
    public function setSecret(string $secret): void
    {
        $secret = $this->escapeData($secret);
        echo "::add-mask::$secret"."\n";
    }

    /**
     * Prepends inputPath to the PATH (for this action and future actions).
     *
     * @deprecated
     */
    public function addPath(string $inputPath): void
    {
        echo "::add-path::${inputPath}"."\n";
        // $this->warning('add PATH not support');
    }

    /**
     * Gets the value of an input.  The value is also trimmed.
     *
     * @param string $name name of the input to get
     * @returns   string
     */
    public function getInput(string $name, bool $required = false): string
    {
        $value = getenv(strtoupper('INPUT_'.$name));

        $value = false === $value ? '' : $value;

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
        $value = $this->escapeData($value);
        echo "::set-output name=$name::$value"."\n";
    }

    /**
     * Sets the action status to failed.
     * When the action exits it will be with an exit code of 1.
     *
     * @param string $message add error issue message
     */
    public function setFailed(string $message): void
    {
        $message = $this->escapeData($message);
        die('::error::'.$message);
    }

    /**
     * Saves state for current action, the state can only be retrieved by this action's post job execution.
     *
     * @param string $name  name of the state to store
     * @param string $value value to store
     */
    public function saveState(string $name, string $value): void
    {
        $value = $this->escapeData($value);
        echo "::save-state name=$name::$value"."\n";
    }

    /**
     * Gets the value of an state set by this action's main execution.
     *
     * @param string $name name of the state to get
     */
    public function getState(string $name): string
    {
        $value = getenv(strtoupper("STATE_${name}"));

        return false === $value ? '' : $value;
    }

    public function startGroup(string $name): void
    {
        $name = $this->escapeData($name);
        echo "::group::$name"."\n";
    }

    public function info(string $message): void
    {
        echo $message."\n";
    }

    public function endGroup(): void
    {
        echo '::endgroup::'."\n";
    }

    public function escapeData(string $string)
    {
        $string = str_replace('%', '%25', $string);
        $string = str_replace("\n", '%0A', $string);

        return str_replace("\r", '%0D', $string);
    }
}
