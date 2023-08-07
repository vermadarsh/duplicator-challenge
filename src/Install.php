<?php
/**
 * The activation route of the plugin.
 * php version 7.4.1
 *
 * @category   Class
 * @package    DupChallenge
 * @subpackage DupChallenge/src
 * @author     Adarsh Verma <adarsh.srmcem@gmail.com>
 * @license    GPLv2 or later
 * @link       https://github/vermadarsh/
 * @since      1.0.0
 */

namespace DupChallenge;

/**
 * The activation route of the plugin.
 *
 * Defines the functionalities executed on the plugin activation hook.
 *
 * @category   Class
 * @package    DupChallenge
 * @subpackage DupChallenge/src
 * @author     Adarsh Verma <adarsh.srmcem@gmail.com>
 * @license    GPLv2 or later
 * @link       https://github/vermadarsh/
 */
class Install
{
    /**
     * Register install hoosk
     *
     * @return void
     */
    public static function register()
    {
        if (is_admin() ) {
            register_activation_hook(DUP_CHALLENGE_FILE, array( __CLASS__, 'dupRegisterActivationHookCallback' ));
        }
    }

    /**
     * Install plugin
     *
     * @return void
     */
    public static function dupRegisterActivationHookCallback()
    {
        global $wpdb;
        $existing_tables          = ( ! empty($wpdb->tables) && is_array($wpdb->tables) ) ? $wpdb->tables : array();
        $dup_site_scanner_log_tbl = 'dup_site_scanner_log';

        // Create the table for storing the site scan log.
        if (! empty($existing_tables) && is_array($existing_tables) && ! in_array($yt_table_name, $existing_tables) ) {
            $charset_collate          = $wpdb->get_charset_collate();
            $dup_site_scanner_log_tbl = $wpdb->prefix . 'dup_site_scanner_log';
            $sql              = "CREATE TABLE `$dup_site_scanner_log_tbl` (
				`id` int(255) NOT NULL AUTO_INCREMENT,
				`filename` varchar(200) DEFAULT '' NOT NULL,
				`path` longtext DEFAULT '' NOT NULL,
				`type` varchar(200) DEFAULT '' NOT NULL,
				`extension` varchar(200) DEFAULT '' NOT NULL,
				`size` longtext DEFAULT '' NOT NULL,
				`file_permissions` varchar(200) DEFAULT '' NOT NULL,
				`nodes_count` int(200) DEFAULT 0 NOT NULL,
				PRIMARY KEY  ( id )
			) {$charset_collate};";
            
            include_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($sql);
        }

        // Set the activation redirect.
        add_option('dup_do_activation_redirect', 1);
    }
}
