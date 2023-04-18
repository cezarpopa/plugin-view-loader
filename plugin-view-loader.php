<?php
declare(strict_types=1);

/**
 * Plugin Name: Plugin View Loader
 * Plugin URI: https://github.com/cezarpopa/plugin-view-loader/
 * Description: Untested attempt at creating a mu-plugin to use in WordPress plugins to load partial templates.
 * Author: Cezar Popa
 * Version: 0.1
 * Author URI: https://github.com/cezarpopa
 */

if ( ! function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

/**
 * @see     https://developer.wordpress.org/reference/functions/get_template_part/
 * @example get_plugin_part('your-plugin-name/path-to-view/slug', 'name', ['foo' => 'bar']);
 *
 * @param string      $slug
 * @param string|null $name
 * @param array       $args
 *
 * @return string
 */
function get_plugin_part(
    string $slug,
    string $name = null,
    array $args = []
): string {
    if (empty($slug)) {
        return '';
    }

    do_action("get_plugin_part_{$slug}", $slug, $name, $args);

    $templates = [];
    if (isset($name)) {
        $templates[] = "{$slug}-{$name}.php";
    }

    $templates[] = "{$slug}.php";

    return get_plugin_partial_path($templates, true, $args);
}

/**
 * @param array $templateNames
 * @param bool  $requireOnce
 * @param array $args
 *
 * @return string
 */
function get_plugin_partial_path(
    array $templateNames,
    bool $requireOnce = true,
    array $args = []
): string {
    $located = '';

    foreach ($templateNames as $templateName) {
        if ( ! $templateName) {
            continue;
        }

        $filePath = plugin_dir_path(__DIR__) . $templateName;

        if (file_exists($filePath)) {
            $located = $filePath;
            break;
        }
    }

    if ($located !== '') {
        ob_start();

        try {
            load_template($located, $requireOnce, $args);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return ob_get_clean();
    }

    return $located;
}
