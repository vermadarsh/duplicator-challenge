<?php
/**
 * Singleton class controller for admin pages.
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

namespace DupChallenge\Controllers;

use DupChallenge\Views\Main\MainPageView;

/**
 * Singleton class controller for admin pages.
 *
 * Defines the controller hooks to run on the admin side.
 *
 * @category   Class
 * @package    DupChallenge
 * @subpackage DupChallenge/src/Controllers
 * @author     Adarsh Verma <adarsh.srmcem@gmail.com>
 * @license    GPLv2 or later
 * @link       https://github/vermadarsh/
 */
class AdminPagesController extends AbstractController
{
    const MAIN_PAGE_SLUG = 'duplicator-challenge';

    /**
     * Class constructor
     */
    protected function __construct()
    {
    }

    /**
     * Add admin javascripts
     *
     * @return void
     */
    public static function adminScripts()
    {
        if (! self::isPluginAdminPage() ) {
            return;
        }

        // Set the asset file suffix.
        $suffix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';

        // Enqueue the admin jquery script file.
        if (! wp_script_is('duplicator-challenge-admin-scripts') ) {
            wp_enqueue_script(
                'duplicator-challenge-admin-scripts',
                DUP_CHALLENGE_URL . "/assets/js/admin{$suffix}.js",
                array( 'jquery' ),
                DUP_CHALLENGE_VERSION,
                true
            );

            // Localize the admin script.
            wp_localize_script(
                'duplicator-challenge-admin-scripts',
                'DuplicatorAdminJsObj',
                array(
                'ajaxurl'          => admin_url('admin-ajax.php'),
                'scan_in_progress' => __('Scanning in progress...', 'dup-challenge'),
                'scan_completed'   => __('Scanning complete...', 'dup-challenge'),
                'ajax_nonce'       => wp_create_nonce('dup-ajax-nonce'),
                )
            );
        }
    }

    /**
     * Add admin styles
     *
     * @return void
     */
    public static function adminStyles()
    {
        if (! self::isPluginAdminPage() ) {
            return;
        }

        // Set the asset file suffix.
        $suffix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';

        wp_enqueue_style(
            'duplicator-challenge-admin-styles',
            DUP_CHALLENGE_URL . "/assets/css/admin{$suffix}.css",
            array(),
            DUP_CHALLENGE_VERSION
        );
    }

    /**
     * Check if current page is plugin admin page
     *
     * @return bool
     */
    public static function isPluginAdminPage()
    {
        $page  = sanitize_text_field(( isset($_REQUEST['page']) ? $_REQUEST['page'] : '' ));
        $pages = array( self::MAIN_PAGE_SLUG );

        return in_array($page, $pages);
    }

    /**
     * Main page action
     *
     * @return void
     */
    public function mainPageAction()
    {
        MainPageView::renderMainPage();
    }
}
