<?php
/**
 * Plugin Name: Shortcode Source Debugger
 * Description: A utility to find the source code path of the function associated with a shortcode in WordPress. [shortcode_source name="shortcode_name"]
 * Version: 1.0
 * Author: Your Name
 * License: GPL2
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get the source code location of a shortcode's callback function.
 *
 * @param string $shortcode The shortcode name.
 * @return string The file path and line number of the shortcode function, or a message if not found.
 */
function get_shortcode_source($shortcode) {
    global $shortcode_tags;

    // Check if the shortcode exists
    if (!shortcode_exists($shortcode)) {
        return "Shortcode '{$shortcode}' does not exist.";
    }

    // Get the function associated with the shortcode
    $function = $shortcode_tags[$shortcode];

    // Handle closures or anonymous functions
    if (is_object($function) && ($function instanceof Closure)) {
        return "The shortcode '{$shortcode}' uses an anonymous function or closure.";
    }

    // Reflection: Determine if it's a class method or standard function
    if (is_array($function)) {
        $reflection = new ReflectionMethod($function[0], $function[1]);
    } else {
        $reflection = new ReflectionFunction($function);
    }

    // Return the file path and line number
    return "Shortcode '{$shortcode}' is declared in {$reflection->getFileName()} at line {$reflection->getStartLine()}.";
}

/**
 * List all registered shortcodes and their callback functions.
 *
 * Outputs a formatted list of shortcodes and their associated functions.
 */
function list_all_shortcodes() {
    global $shortcode_tags;

    echo '<h2>Registered Shortcodes</h2>';
    echo '<ul>';
    foreach ($shortcode_tags as $shortcode => $function) {
        echo '<li><strong>' . esc_html($shortcode) . '</strong>: ';

        if (is_array($function)) {
            echo is_object($function[0]) 
                ? get_class($function[0]) . '::' . $function[1] 
                : $function[0] . '::' . $function[1];
        } elseif (is_object($function) && ($function instanceof Closure)) {
            echo 'Anonymous function or closure';
        } else {
            echo $function;
        }

        echo '</li>';
    }
    echo '</ul>';
}

/**
 * Shortcode to display the source of a specified shortcode.
 *
 * Usage: [shortcode_source name="shortcode_name"]
 *
 * @param array $atts The shortcode attributes.
 * @return string The source location of the specified shortcode.
 */
function shortcode_source_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'name' => '',
        ),
        $atts,
        'shortcode_source'
    );

    if (empty($atts['name'])) {
        return 'Please provide a shortcode name using the "name" attribute.';
    }

    return '<pre>' . esc_html(get_shortcode_source($atts['name'])) . '</pre>';
}

add_shortcode('shortcode_source', 'shortcode_source_shortcode');
