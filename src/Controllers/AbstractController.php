<?php
/**
 * Abstract class controller.
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

/**
 * Abstract class controller.
 *
 * Abstract class to manage all the controllers.
 *
 * @category   Class
 * @package    DupChallenge
 * @subpackage DupChallenge/src/Controllers
 * @author     Adarsh Verma <adarsh.srmcem@gmail.com>
 * @license    GPLv2 or later
 * @link       https://github/vermadarsh/
 */
abstract class AbstractController
{
    /**
     * The list of controller instances.
     *
     * @access private
     * @var    array    $_instances    The list of controller instances.
     */
    private static $_instances = array();

    /**
     * Get instance
     *
     * @return static
     */
    public static function getController()
    {
        $class = get_called_class();
        if (!isset(self::$_instances[$class])) {
            self::$_instances[$class] = new static();
        }

        return self::$_instances[$class];
    }

    /**
     * Class constructor
     */
    abstract protected function __construct();
}
