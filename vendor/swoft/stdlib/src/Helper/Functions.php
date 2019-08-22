<?php declare(strict_types=1);
if (!function_exists('value')) {
    /**
     * Return the callback value
     *
     * @param mixed $value
     *
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('fnmatch')) {
    /**
     * @param string $pattern
     * @param string $string
     *
     * @return bool
     */
    function fnmatch(string $pattern, string $string): bool
    {
        return 1 === preg_match(
                '#^' . strtr(preg_quote($pattern, '#'), ['\*' => '.*', '\?' => '.']) . '$#i',
                $string
            );
    }
}

if (!function_exists('tap')) {
    /**
     * Call the given Closure with the given value then return the value.
     *
     * @param mixed   $value
     * @param Closure $callback
     *
     * @return mixed
     */
    function tap($value, Closure $callback = null)
    {
        if (!$callback) {
            return $value;
        }

        $callback($value);

        return $value;
    }
}

if (!function_exists('printr')) {
    /**
     * Print data like print_r, but allow multi params
     *
     * @param mixed ...$vars
     */
    function printr(...$vars)
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        $line = $trace[0]['line'];
        $pos  = $trace[1]['class'] ?? $trace[0]['file'];

        if ($pos) {
            echo "CALL ON $pos($line):\n";
        }

        foreach ($vars as $var) {
            /** @noinspection ForgottenDebugOutputInspection */
            print_r($var);
            echo PHP_EOL;
        }
    }
}

if (!function_exists('vdump')) {
    /**
     * Dump data like var_dump
     *
     * @param mixed ...$vars
     */
    function vdump(...$vars)
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        $line = $trace[0]['line'];
        $pos  = $trace[1]['class'] ?? $trace[0]['file'];

        if ($pos) {
            echo "CALL ON $pos($line):\n";
        }

        ob_start();
        /** @noinspection ForgottenDebugOutputInspection */
        var_dump(...$vars);

        $string = ob_get_clean();

        echo preg_replace(
            ["/Array[\s]*\(/", "/=>[\s]+/i"],
            ['Array (', '=> '],
            $string
        );
    }
}
