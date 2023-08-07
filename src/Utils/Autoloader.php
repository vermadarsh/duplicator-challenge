<?php
/**
 * Plugin autoloader class.
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

namespace DupChallenge\Utils;

/**
 * Plugin autoloader class.
 *
 * Defines the main entrance of the plugin.
 *
 * @category   Class
 * @package    DupChallenge
 * @subpackage DupChallenge/src/Views/Main
 * @author     Adarsh Verma <adarsh.srmcem@gmail.com>
 * @license    GPLv2 or later
 * @link       https://github/vermadarsh/
 */
final class Autoloader
{
    const ROOT_NAMESPACE = 'DupChallenge\\';

    /**
     * Register autoloader function
     *
     * @return void
     */
    public static function register()
    {
        spl_autoload_register(array(__CLASS__, 'load'));
    }

    /**
     * Load class
     *
     * @param string $className class name
     *
     * @return bool return true if class is loaded
     */
    public static function load($className)
    {
        foreach (self::getNamespacesMapping() as $namespace => $mappedPath) {
            if (strpos($className, $namespace) !== 0) {
                continue;
            }

            $filepath = $mappedPath . str_replace('\\', '/', substr($className, strlen($namespace))) . '.php';
            if (file_exists($filepath)) {
                include_once $filepath;
                return true;
            }
        }

        return false;
    }

    /**
     * Return namespace mapping
     *
     * @return string[]
     */
    protected static function getNamespacesMapping()
    {
        // The order is important, its necessary inserting the longest namespaces first.
        return array(
            self::ROOT_NAMESPACE => DUP_CHALLENGE_PATH . '/src/'
        );
    }
}
