<?php
/**
 * The plugin bootstrap file.
 * php version 7.4.1
 *
 * @category File
 * @package  DupChallenge
 * @author   Adarsh Verma <adarsh.srmcem@gmail.com>
 * @license  GPLv2 or later
 * @link     https://github/vermadarsh/
 *
 * Plugin Name: Duplicator Challenge
 * Plugin URI: https://github.com/vermadarsh/duplicator-challenge/
 * Description: Duplicator Challenge
 * Version: 1.0.0
 * Requires at least: 5.2
 * Tested up to: 6.2.2
 * Requires PHP: 5.6.20
 * Author: Adarsh Verma
 * Author URI: https://github.com/vermadarsh/
 * Text Domain: dup-challenge
 */

defined('ABSPATH') || exit;
define('DUP_CHALLENGE_VERSION', '1.0.0');
define('DUP_CHALLENGE_PATH', __DIR__);
define('DUP_CHALLENGE_FILE', __FILE__);
define('DUP_CHALLENGE_URL', plugins_url('', DUP_CHALLENGE_FILE));

require_once DUP_CHALLENGE_PATH . '/src/Utils/Autoloader.php';
DupChallenge\Utils\Autoloader::register();
DupChallenge\Bootstrap::init();
