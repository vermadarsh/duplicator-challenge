<?php
/**
 * Plugin bootstrap class.
 * php version 7.4.1
 *
 * @category   Class
 * @package    DupChallenge
 * @subpackage DupChallenge/src/Controllers
 * @author     Adarsh Verma <adarsh.srmcem@gmail.com>
 * @license    GPLv2 or later
 * @link       https://github/vermadarsh/
 * @since      1.0.0
 */

namespace DupChallenge;

use DupChallenge\Controllers\AdminPagesController;

/**
 * Plugin bootstrap class.
 *
 * Defines the main routing (hooks) of the controllers.
 *
 * @category   Class
 * @package    DupChallenge
 * @subpackage DupChallenge/src/Views/Main
 * @author     Adarsh Verma <adarsh.srmcem@gmail.com>
 * @license    GPLv2 or later
 * @link       https://github/vermadarsh/
 */
class Bootstrap
{
    /**
     * Init plugin
     *
     * @return void
     */
    public static function init()
    {
        Install::register();
        Unistall::register();

        add_action('admin_init', array( __CLASS__, 'dupAdminInitCallback' ));
        add_action('admin_menu', array( __CLASS__, 'dupAdminMenuCallback' ));
        add_action('wp_ajax_scan_site', array( __CLASS__, 'dupScanSiteCallback' ));
        add_action('wp_ajax_scan_last_set', array( __CLASS__, 'dupScanLastSetCallback' ));
    }

    /**
     * Init admin
     *
     * @return void
     */
    public static function dupAdminInitCallback()
    {
        add_action('admin_enqueue_scripts', array( AdminPagesController::class, 'adminScripts' ));
        add_action('admin_enqueue_scripts', array( AdminPagesController::class, 'adminStyles' ));

        // Redirect after plugin redirect.
        if (get_option('dup_do_activation_redirect') ) {
            delete_option('dup_do_activation_redirect');
            wp_safe_redirect(admin_url('admin.php?page=duplicator-challenge'));
            exit;
        }
    }

    /**
     * Init menu
     *
     * @return void
     */
    public static function dupAdminMenuCallback()
    {
        // Add the menu page.
        add_menu_page(
            __('Duplicator Plugin Challenge', 'dup-challenge'),
            __('Site Scanner', 'dup-challenge'),
            'manage_options',
            AdminPagesController::MAIN_PAGE_SLUG,
            array( AdminPagesController::getController(), 'mainPageAction' ),
            'dashicons-admin-generic',
            100
        );
    }

    /**
     * Ajax callback to scan the site.
     *
     * @since  1.0.0
     * @return void
     */
    public static function dupScanSiteCallback()
    {
        // Check for nonce security.
        $nonce = filter_input(INPUT_POST, 'nonce', FILTER_SANITIZE_STRING);
        if (! wp_verify_nonce($nonce, 'dup-ajax-nonce') ) {
            wp_send_json_error(
                array(
                'code'          => 'scan-failed',
                'error_message' => __('Site could not be scanned as nonce couldn\'t be validated. Please contact the administrator.', 'dup-challenge'),
                )
            );
            wp_die();
        }

        // Posted variables.
        $root_dir            = rtrim(ABSPATH, '/');
        $files_per_iteration = (int) filter_input(INPUT_POST, 'files_per_iteration', FILTER_SANITIZE_NUMBER_INT);
        $last_scanned_index  = (int) filter_input(INPUT_POST, 'last_scanned_index', FILTER_SANITIZE_NUMBER_INT);
        $page                = (int) filter_input(INPUT_POST, 'page', FILTER_SANITIZE_NUMBER_INT);
        $scannedDir          = new \RecursiveDirectoryIterator($root_dir);
        $i                   = 1;
        $html                = '';

        // If it's the first request, clean the database table.
        if (1 === $page ) {
            self::_dupCleanDbTable();
        }

        foreach ( new \RecursiveIteratorIterator($scannedDir) as $filename => $file ) {
            // Skip, if the current file/directory is a dot.
            if ('..' === $file->getBasename() ) {
                continue;
            }

            // If the filename is empty, the scanning is completed.
            if (empty($filename) ) {
                // Return the ajax response.
                wp_send_json_success(array( 'code' => 'scan-complete' ));
                wp_die();
            }

            // Break the loop if 200 files are traversed.
            if (( $page * $files_per_iteration ) === $i ) {
                $last_scanned_index = $page * $files_per_iteration;
                break;
            }

            // Iterate through the last scanned index and ignore the scanned ones.
            if ($i <= $last_scanned_index ) {
                $i++; // Increase the iterator.
                continue; // Skip the file iteration.
            }

            // Get the number of nodes.
            if ($file->isFile() ) {
                $node_count = 1;
            } elseif ($file->isDir() ) {
                $node_count = count(scandir($filename)) - 2; // Subtracted 2 to disregard the current and the parent folder.
            }

            // Add the row.
            $html .= '<tr>';
            $html .= '<td>' . $filename . '</td>';
            $html .= '<td>' . $file->getSize() . ' bytes' . '</td>';
            $html .= '<td>' . sprintf(_n('%s node', '%s nodes', $node_count, 'dup-challenge'), number_format_i18n($node_count)) . '</td>';
            $html .= '</tr>';

            // Insert the data into the database.
            self::_dupInsertIntoDatabase(
                array(
                'filename'         => ( $file->isDir() ) ? basename(dirname($filename)) : basename($filename),
                'path'             => $filename,
                'type'             => ( $file->isDir() ) ? 'dir' : 'file',
                'extension'        => ( $file->isDir() ) ? '' : pathinfo($filename, PATHINFO_EXTENSION),
                'size'             => $file->getSize(),
                'file_permissions' => $file->getPerms(),
                'nodes_count'      => $node_count,
                )
            );

            // Increase the iterator.
            $i++;
        }

        // Return the ajax response.
        wp_send_json_success(
            array(
            'code'               => 'scan-in-progress',
            'html'               => $html,
            'last_scanned_index' => $last_scanned_index,
            )
        );
        wp_die();
    }

    /**
     * Insert the row data into the database.
     *
     * @param array $db_row Database row.
     *
     * @return int
     */
    private static function _dupInsertIntoDatabase( $db_row = array() )
    {
        global $wpdb;

        // Return, if the db row is empty or not array.
        if (empty($db_row) || ! is_array($db_row) ) {
            return;
        }

        // Insert the data now.
        $wpdb->insert(
            $wpdb->prefix . 'dup_site_scanner_log',
            $db_row,
            array(
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            )
        );

        return $wpdb->insert_id;
    }

    /**
     * Clean the database table.
     *
     * @since  1.0.0
     * @return void
     */
    private static function _dupCleanDbTable()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dup_site_scanner_log';

        $wpdb->query("TRUNCATE TABLE `{$table_name}`");
    }
}
