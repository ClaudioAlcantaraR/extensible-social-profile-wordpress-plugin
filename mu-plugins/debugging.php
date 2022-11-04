<?php

/**
 * Enhanced debugging function wich formats debug info and allows for a label
 * 
 * @param mixed $data The data dump.
 * @param string $label A label to apply before the data.
 * @return mixed The dump and the label if present.
 */

function wp_var_dump($data, $label = '')
{
    echo '<div style="background-color: #f2f2f2; padding: 12px;">';

    if(!empty($label))
    {
        echo '<h2>' . $label . '</h2>';
    }

    echo '<pre>'; var_dump($data); echo '</pre></div>';
    return;
    
}

/**
 * Write errors to a log file named debug.log in wp-content
 * @param mixed $log the thing you want to log
 */

function ca_write_log()
{
    if (true === WP_DEBUG) {
        if(is_array($log) || is_object($log))
        {
            error_log(print_r($log, true));
        } else {
            error_log($log);
        }
    }
}