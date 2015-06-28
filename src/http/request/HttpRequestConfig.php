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
use soloproyectos\text\Text;

/**
 * Class HttpRequestConfig.
 *
 * @package Http
 * @author  Gonzalo Chumillas <gchumillas@email.com>
 * @license https://github.com/soloproyectos-php/http/blob/master/LICENSE The MIT License (MIT)
 * @link    https://github.com/soloproyectos-php/http
 */
class HttpRequestConfig
{
    /**
     * Associative array of HTTP options.
     * See http://php.net/manual/en/context.http.php for more info.
     * @var array of strings
     */
    private $_options = array(
        "header" => "Content-Type: application/x-www-form-urlencoded; charset=utf-8"
    );

    /**
     * Gets HTTP option.
     *
     * @param string $name Option name
     *
     * @return string
     */
    public function getOption($name)
    {
        return Arr::get($this->_options, $name);
    }

    /**
     * Sets HTTP option.
     *
     * @param string $name  Option name
     * @param string $value HTTP option
     *
     * @return void
     */
    public function setOption($name, $value)
    {
        Arr::set($this->_options, $name, $value);
    }

    /**
     * Gets list of options.
     *
     * @return array of string
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Gets header key.
     *
     * @param string $name Header key
     *
     * @return string
     */
    public function getHeaderKey($name)
    {
        $ret = "";
        $regexp = '/^\s*' . preg_quote($name) . '\s*\:(.*)/mi';

        if (preg_match($regexp, $this->getOption("header"), $matches)) {
            $ret = trim($matches[1]);
        }

        return $ret;
    }

    /**
     * Sets header key.
     *
     * @param string $name  Header key
     * @param string $value Value
     *
     * @return void
     */
    public function setHeaderKey($name, $value)
    {
        // fixes headers by replacing '\n' by '\r\n'
        $header = $this->getOption("header");
        $header = str_replace(array("\r", "\n"), array("", "\r\n"), $header);

        // replaces entry
        $count = 0;
        $regexp = '/^(\s*' . preg_quote($name) . '\s*)\:(.*)/mi';
        $header = preg_replace($regexp, "\$1: $value", $header, -1, $count);

        // ... or appends entry
        if ($count == 0) {
            $header = Text::concat("\r\n", $header, "$name: $value");
        }

        $this->setOption("header", $header);
    }

    /**
     * Gets the type of the Content-Type key.
     *
     * This function gets the type of the Content-Type key, ignoring other parameters, like the charset.
     *
     * @return string
     */
    public function getContentType()
    {
        $options = explode(";", $this->getHeaderKey("Content-Type"));
        return trim($options[0]);
    }

    /**
     * Sets the type of the Content-Type key.
     *
     * This function sets the type of the Content-Type key, ignoring other parameters, like the charset.
     *
     * @param string $value Type of the Content-Type
     *
     * @return void
     */
    public function setContentType($value)
    {
        $options = explode(";", $this->getHeaderKey("Content-Type"));
        $options[0] = trim($value);
        $this->setHeaderKey("Content-Type", implode(";", $options));
    }

    /**
     * Gets a Content-Type option.
     *
     * For example:
     * ```php
     * echo $config->getContentTypeOption("charset");
     * ```
     *
     * @param string $name Option name
     *
     * @return string
     */
    public function getContentTypeOption($name)
    {
        $ret = "";

        $contentType = $this->getHeaderKey("Content-Type");
        if (preg_match('/;\s*' . preg_quote($name) . '\s*\=(.*)$/i', $contentType, $matches)) {
            $ret = trim($matches[1]);
        }

        return $ret;
    }

    /**
     * Sets a Content-Type option
     *
     * For example:
     * ```php
     * $config->setContentTypeOption("charset", "iso-8859-1");
     * ```
     *
     * @param string $name  Option name
     * @param string $value Value
     *
     * @return void
     */
    public function setContentTypeOption($name, $value)
    {
        // replaces entry
        $count = 0;
        $regexp = '/;\s*(' . preg_quote($name) . ')\s*\=\s*([^;]*)/i';
        $contentType = preg_replace(
            $regexp, "; \$1=$value", $this->getHeaderKey("Content-Type"), -1, $count
        );

        // ... or appends entry
        if ($count == 0) {
            $contentType .= "; $name=$value";
        }

        $this->setHeaderKey("Content-Type", $contentType);
    }
}
