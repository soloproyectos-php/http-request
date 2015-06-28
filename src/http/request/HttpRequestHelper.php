<?php
/**
 * This file is part of SoloProyectos common library.
 *
 * @author  Gonzalo Chumillas <gchumillas@email.com>
 * @license https://github.com/soloproyectos-php/http/blob/master/LICENSE The MIT License (MIT)
 * @link    https://github.com/soloproyectos-php/http
 */
namespace soloproyectos\http\request;
use soloproyectos\arr\Arr;

/**
 * Class HttpRequest.
 *
 * This class is used to access the request variables.
 *
 * @package Http\Request
 * @author  Gonzalo Chumillas <gchumillas@email.com>
 * @license https://github.com/soloproyectos-php/http/blob/master/LICENSE The MIT License (MIT)
 * @link    https://github.com/soloproyectos-php/http
 */
class HttpRequest
{
    /**
     * Gets a request attribute.
     *
     * @param string $name    Request attribute.
     * @param string $default Default value (not required)
     *
     * @return mixed
     */
    public static function get($name, $default = null)
    {
        $param = Arr::get($_REQUEST, $name, $default);

        if ($_SERVER["REQUEST_METHOD"] == "GET" && is_string($param)) {
            $param = urldecode($param);
        }

        return $param;
    }

    /**
     * Sets a request attribute.
     *
     * @param string $name  Request attribute.
     * @param mixed  $value Request value.
     *
     * @return void
     */
    public static function set($name, $value)
    {
        $_REQUEST[$name] = $value;
    }

    /**
     * Does the request attribute exist?
     *
     * @param string $name Request attribute.
     *
     * @return boolean
     */
    public static function is($name)
    {
        return Arr::is($_REQUEST, $name);
    }

    /**
     * Deletes a request attribute.
     *
     * @param string $name Request attribute.
     *
     * @return void
     */
    public static function del($name)
    {
        Arr::del($_REQUEST, $name);
    }
}
