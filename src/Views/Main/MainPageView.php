<?php
/**
 * The main view of the plugin configuration into the admin screen.
 * php version 7.4.1
 *
 * @category   Class
 * @package    DupChallenge
 * @subpackage DupChallenge/src/Views/Main
 * @author     Adarsh Verma <adarsh.srmcem@gmail.com>
 * @license    GPLv2 or later
 * @link       https://github/vermadarsh/
 * @since      1.0.0
 */

namespace DupChallenge\Views\Main;

/**
 * The main view of the plugin configuration into the admin screen.
 *
 * Defines the template displayed on the main screen in admin screen.
 *
 * @category   Class
 * @package    DupChallenge
 * @subpackage DupChallenge/src/Views/Main
 * @author     Adarsh Verma <adarsh.srmcem@gmail.com>
 * @license    GPLv2 or later
 * @link       https://github/vermadarsh/
 */
class MainPageView
{
    /**
     * Render main page
     *
     * @return void
     */
    public static function renderMainPage()
    {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <p><?php esc_html_e('Click the button below to scan this website.', 'dup-challenge');?></p>
            <a class="button dup-scan-site" href="#" title="<?php esc_html_e('Scan Site', 'dup-challenge'); ?>"><?php esc_html_e('Scan Site', 'dup-challenge'); ?></a>
            <table class="scanned-dirs form-table d-none">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Path', 'dup-challenge'); ?></th>
                        <th><?php esc_html_e('Size', 'dup-challenge'); ?></th>
                        <th><?php esc_html_e('Nodes', 'dup-challenge'); ?></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <p class="scan-in-progress d-none"><?php esc_html_e('Please wait...', 'dup-challenge');?></p>
        </div>
        <?php
    }
}
