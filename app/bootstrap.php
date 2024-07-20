<?php

if (!function_exists('dd')) {
    /**
     * Dump and die. Outputs information about variables and stops execution.
     *
     * @param mixed ...$vars
     * @return void
     */
    function dd(...$vars): void
    {
        ob_start();
        
        echo "<pre style='background: #f8f9fa; padding: 1em; border: 1px solid #dee2e6; border-radius: .25rem;'>";
        foreach ($vars as $var) {
            var_export($var);
            echo "\n";
        }
        
        $error = error_get_last();
        if ($error) {
            echo "\n[ERROR]: ";
            var_export($error);
        }

        echo "</pre>";

        ob_end_flush();
        die();
    }
}
