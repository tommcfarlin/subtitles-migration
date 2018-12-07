<?php
/**
 * Subtitle Migration
 *
 * Migrates old theme subtitles to compatibility with the Subtitles plugin. This is for developers. Review the readme.
 *
 * @package   TomMcFarlin
 * @author    Tom McFarlin <tom@tommcfarlin.com>
 * @license   GPL-3.0+
 * @link      https://github.com/tommcfarlin/subtitles-migration
 * @copyright 2018 Tom McFarlin
 *
 * @wordpress-plugin
 * Plugin Name:       Subtitle Migration
 * Plugin URI:        https://github.com/tommcfarlin/subtitles-migration/
 * Description:       Migrates old theme subtitles to compatibility with the Subtitles plugin.
 * Version:           0.1.0
 * Author:            Tom McFarlin
 * Author URI:        https://tommcfarlin.com
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * GitHub Plugin URI: https://github.com/tommcfarlin/subtitles-migration/
 */

namespace TomMcFarlin;

defined('WPINC') || die;

add_action('plugins_loaded', __NAMESPACE__ . '\\migrationHeadsUp');
/**
 * Give the user a heads up that this plugin should only be run by developers and that
 * they should understand the implications of what they are about to do.
 */
function migrationHeadsUp()
{
    if (!_userHasPermission()) {
        return;
    }

    add_action('admin_notices', function () {

        // Create the new URL for kicking off the migration.
        $url = add_query_arg(
            'subtitle-migration',
            'start',
            filter_input(INPUT_SERVER, 'HTTP_REFERER')
        );

        // Set the class so users have a heads up.
        $class = 'notice notice-warning';

        // Build the message.
        $message = '<h3><em>Subtitle Migration</em> is for developers only.</h3>';
        $message .= '<hr />';
        $message .= '<ul><li>Review the README for how to work with this plugin.</li>';
        $message .= '<li>Once the plugin is complete, you can deactivate the plugin.</li></ul>';
        $message .= '<p>Ready to go? <a href="' . $url . '">Migrate Subtitles</a>.</p>';
        $message .= '<hr />';

        // Render the message.
        printf(
            '<div class="%1$s"><p>%2$s</p></div>',
            esc_attr($class),
            $message
        );
    });
}

add_action('plugins_loaded', __NAMESPACE__ . '\\updateAmpersandSubtitles');
/**
 * I used a theme that had a neat 'subtitle' function. When I changed themes,
 * I decided to use a another Subtitle plugin (which I'll) link below)[0].
 *
 * This function updates the post metadata keys so that any subtitles created
 * with the first theme's functionality to use the new subtitle plugin.
 *
 * Please view the documentation in the code to see what keys you may need
 * to change for your own work.
 *
 * [0] Subtitles Plugin: https://wordpress.org/plugins/subtitles/
 */
function updateAmpersandSubtitles()
{
    if (!_canRunQuery()) {
        return;
    }

    $oldMetaKey = '_ampersand_subtitle_value'; // This key represents the original key.
    $newMetaKey = '_subtitle'; // This is the key for the Subtitles plugin.

    global $wpdb;
    $results = $wpdb->update(
        $wpdb->postmeta,
        ['meta_key' => $newMetaKey],
        ['meta_key' => $oldMetaKey],
        ['%s', '%s']
    );
    error_log(print_r($results, true));

    if (false === $results) {
        wp_safe_redirect(
            add_query_arg(
                'subtitle-migration',
                'error',
                filter_input(INPUT_SERVER, 'HTTP_REFERER')
            ),
            200
        );
        exit;
    }

    wp_safe_redirect(
        add_query_arg(
            'subtitle-migration',
            'complete',
            filter_input(INPUT_SERVER, 'HTTP_REFERER')
        ),
        200
    );
    exit;
}

add_action('plugins_loaded', __NAMESPACE__ . '\\test');
/**
 * TODO:
 */
function test()
{
    if (_migrationError()) {
        _displayErrorMessage();
    }

    if (_migrationComplete()) {
        _displaySuccessMessage();
    }
}

/**
 * @return bool True if we're on the plugins page and the user has proper permissions.
 */
function _userHasPermission()
{
    return
        ('/wp-admin/plugins.php' === filter_input(INPUT_SERVER, 'DOCUMENT_URI')) &&
        current_user_can('manage_options') &&
        'complete' !== filter_input(INPUT_GET, 'subtitle-migration') &&
        'error' !== filter_input(INPUT_GET, 'subtitle-migration');
}

/**
 * @return bool True if we're on the plugins page, the user has proper permissions, the query string is set.
 */
function _canRunQuery()
{
    return
        _userHasPermission() &&
        'start' === filter_input(INPUT_GET, 'subtitle-migration');
}

/**
 * @return bool True if the migration has completed without error.
 */
function _migrationComplete()
{
    return 'complete' === filter_input(INPUT_GET, 'subtitle-migration');
}

/**
 * @return bool True if the migration has completed with an error.
 */
function _migrationError()
{
    return 'error' === filter_input(INPUT_GET, 'subtitle-migration');
}

/**
 * Displays an error message if there was a problem running the query.
 */
function _displayErrorMessage()
{
    add_action('admin_notices', function () {
        $class = 'notice notice-error';
        $message = '<p>There was a problem migrating the meta keys. Please see the error log for more details.</p>';
        printf(
            '<div class="%1$s"><p>%2$s</p></div>',
            esc_attr($class),
            $message
        );
    });
}

/**
 * Displays a success message if the query was successful.
 */
function _displaySuccessMessage()
{
    add_action('admin_notices', function () {
        $class = 'notice notice-success';
        $message = '<p><strong>Done.</strong> You may now deactivate the plugin.</p>';
        printf(
            '<div class="%1$s"><p>%2$s</p></div>',
            esc_attr($class),
            $message
        );
    });
}
