<?php
/**
 * This file is part of SoloProyectos common library.
 *
 * @author  Gonzalo Chumillas <gchumillas@email.com>
 * @license https://github.com/soloproyectos-php/http/blob/master/LICENSE The MIT License (MIT)
 * @link    https://github.com/soloproyectos-php/http
 */
namespace soloproyectos\http\request;
use soloproyectos\text\Text;

/**
 * Class HttpRequestPost.
 *
 * This class is used to send POST requests.
 *
 * @package Http
 * @author  Gonzalo Chumillas <gchumillas@email.com>
 * @license https://github.com/soloproyectos-php/http/blob/master/LICENSE The MIT License (MIT)
 * @link    https://github.com/soloproyectos-php/http
 */
class HttpRequestPost extends HttpRequestAbstract
{
    /**
     * Prepares the request.
     *
     * This function implements HttpRequestAbstract::prepare().
     *
     * @param HttpRequestConfig $config Configuration instance
     *
     * @return void
     */
    protected function prepare($config)
    {
        $config->setOption("method", "POST");
        $config->setContentType("multipart/form-data");
    }
}
