<?php
/**
 * The deactivation route of the plugin.
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
 * The deactivation route of the plugin.
 *
 * Defines the functionalities executed on the plugin deactivation event.
 *
 * @category   Class
 * @package    DupChallenge
 * @subpackage DupChallenge/src
 * @author     Adarsh Verma <adarsh.srmcem@gmail.com>
 * @license    GPLv2 or later
 * @link       https://github/vermadarsh/
 */
class Unistall
{
    /**
     * Register unistall hoosk
     *
     * @return void
     */
    public static function register()
    {
        if (is_admin() ) {
            register_deactivation_hook(DUP_CHALLENGE_FILE, array( __CLASS__, 'dupRegisterDeactivationHookCallback' ));
        }
    }

    /**
     * Deactivation Hook
     *
     * @return void
     */
    public static function dupRegisterDeactivationHookCallback()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dup_site_scanner_log';

        // Drop the table.
        $wpdb->query("DROP TABLE IF EXISTS `{$table_name}`");
    }
}
