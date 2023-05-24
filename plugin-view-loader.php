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

    $templates = [];
    if (isset($name)) {
        $templates[] = "{$slug}-{$name}.php";
    }

    $templates[] = "{$name}.php";

    /**
     * Fires before an attempt is made to locate and load a plugin part.
     *
     * @param string   $slug      The slug name for the generic template.
     * @param string   $name      The name of the specialized template.
     * @param string[] $templates Array of template files to search for, in order.
     * @param array    $args      Additional arguments passed to the template.
     *
     * @since 5.2.0
     * @since 5.5.0 The `$args` parameter was added.
     *
     */
    do_action("get_plugin_part{$slug}", $slug, $name, $args);

    $located = '';

    foreach ($templates as $template_name) {
        if ( ! $template_name) {
            continue;
        }

        if (file_exists(WP_PLUGIN_DIR . $template_name)) {
            $located = WP_PLUGIN_DIR . $template_name;
            break;
        }

        if (file_exists(WPMU_PLUGIN_DIR . $template_name)) {
            $located = WPMU_PLUGIN_DIR . $template_name;
            break;
        }
    }

    if ( !empty($located)) {
        try {
            ob_start();

            get_plugin_partial_view($located, true, $args);

            return ob_get_clean();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    return $located;
}

/**
 * @param string $_template_file
 * @param bool   $require_once
 * @param array  $args
 *
 * @return string
 */
function get_plugin_partial_view(string $_template_file, bool $require_once = true, array $args = []): string
{
    global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;

    if (is_array($wp_query->query_vars)) {
        /*
         * This use of extract() cannot be removed. There are many possible ways that
         * templates could depend on variables that it creates existing, and no way to
         * detect and deprecate it.
         *
         * Passing the EXTR_SKIP flag is the safest option, ensuring globals and
         * function variables cannot be overwritten.
         */
        // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
        extract($wp_query->query_vars, EXTR_SKIP);
    }

    if (isset($s)) {
        $s = esc_attr($s);
    }

    /**
     * Fires before a template file is loaded.
     *
     * @param string $_template_file The full path to the template file.
     * @param bool   $require_once   Whether to require_once or require.
     * @param array  $args           Additional arguments passed to the template.
     *
     * @since 6.1.0
     *
     */
    do_action('wp_before_load_template', $_template_file, $require_once, $args);

    $templateFile = require $_template_file;

    /**
     * Fires after a template file is loaded.
     *
     * @param string $_template_file The full path to the template file.
     * @param bool   $require_once   Whether to require_once or require.
     * @param array  $args           Additional arguments passed to the template.
     *
     * @since 6.1.0
     *
     */
    do_action('wp_after_load_template', $_template_file, $require_once, $args);

    return $templateFile;
}
