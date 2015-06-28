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
use soloproyectos\http\Http;
use soloproyectos\http\request\exception\HttpRequestException;
use soloproyectos\http\request\HttpRequestFormData;
use soloproyectos\text\Text;

/**
 * Class HttpRequestAbstract.
 *
 * @package Http
 * @author  Gonzalo Chumillas <gchumillas@email.com>
 * @license https://github.com/soloproyectos-php/http/blob/master/LICENSE The MIT License (MIT)
 * @link    https://github.com/soloproyectos-php/http
 */
abstract class HttpRequestAbstract
{
    /**
     * Configuration.
     * @var HttpRequestConfig
     */
    protected $config = null;

    /**
     * Associative array of parameters.
     * @var array of strings
     */
    protected $params = array();

    /**
     * Associative array of form parameters.
     * @var array of strings
     */
    protected $formParams = array();

    /**
     * Form boundary.
     * @var string
     */
    private $_formBoundary = "";

    /**
     * Constructor.
     *
     * @param HttpRequestConfig $config Configuration
     */
    public function __construct($config = null)
    {
        $this->_formBoundary = "------" . uniqid("FormBoundary");
        $this->config = $config !== null? $config: new HttpRequestConfig();
        $this->config->setContentTypeOption("boundary", $this->_formBoundary);
    }

    /**
     * Gets a parameter.
     *
     * This function returns `null` if the parameter does not exist.
     *
     * @param string $name Parameter name
     *
     * @return string|scalar[]
     */
    public function getParam($name)
    {
        return Arr::get($this->params, $name);
    }

    /**
     * Sets a parameter value
     *
     * @param string          $name  Parameter name
     * @param scalar|scalar[] $value Value
     *
     * @return void
     */
    public function setParam($name, $value)
    {
        Arr::set($this->params, $name, $value);
    }

    /**
     * Gets a 'form parameter'.
     *
     * This function returns `null` if the parameter does not exist.
     *
     * @param string $name Parameter name
     *
     * @return HttpRequestFormData
     */
    public function getFormParam($name)
    {
        return Arr::get($this->formParams, $name);
    }

    /**
     * Sets a 'form parameter'.
     *
     * @param string                              $name  Parameter name
     * @param scalar|scalar[]|HttpRequestFormData $value Value
     *
     * @return void
     */
    public function setFormParam($name, $value)
    {
        if (!($value instanceof HttpRequestFormData)) {
            $value = new HttpRequestFormData($value);
        }

        Arr::set($this->formParams, $name, $value);
    }

    /**
     * Gets 'form data'.
     *
     * This function is used to set the 'content' option. For example:
     * ```php
     * $config = new HttpRequestConfig();
     * $config->setOption('content', $this->getFormData());
     * ```
     *
     * @return string
     */
    public function getFormData()
    {
        $ret = "";

        if (count($this->formParams) > 0) {
            foreach ($this->formParams as $name => $param) {
                $data = $param->getData();
                $isArray = is_array($data);
                $items = $isArray? $data: array($data);
                $suffix = count($items) > 1? "[]" : "";

                foreach ($items as $key => $item) {
                    $str = "--" . $this->_formBoundary;
                    $suffix = $isArray? "[$key]" : "";

                    $contentDisposition = "Content-Disposition: form-data; name=\""
                        . str_replace("\"", '\"', $name) . "$suffix\"";
                    $filename = $param->getFilename();
                    if (!Text::isEmpty($filename)) {
                        $contentDisposition .= "; filename=" . urlencode($filename);
                    }

                    $contentType = "";
                    $mimeType = $param->getMimeType();
                    if (!Text::isEmpty($mimeType)) {
                        $contentType = "Content-Type: $mimeType";
                    }

                    $content = "\n$item";

                    $str = Text::concat("\n", $str, $contentDisposition, $contentType, $content);
                    $ret = Text::concat("\n", $ret, $str);
                }
            }

            $ret = Text::concat("\n", $ret, "--" . $this->_formBoundary . "--\r\n");
        }

        return $ret;
    }

    /**
     * Prepares the request.
     *
     * This function is called just before sending a request and it is used to prepare the
     * configuration instance.
     *
     * @param HttpRequestConfig $config Configuration instance
     *
     * @return void
     */
    abstract protected function prepare($config);

    /**
     * Sends a HTTP request and returns contents.
     *
     * @param string $url URL
     *
     * @return string
     */
    public function send($url)
    {
        $config = clone $this->config;
        $this->prepare($config);
        if (Text::isEmpty($config->getOption("content"))) {
            $config->setOption("content", $this->getFormData());
        }

        $context = stream_context_create(array("http" => $config->getOptions()));
        $contents = @file_get_contents(Http::addParams($url, $this->params), false, $context);

        // Checks for erros
        // The function file_get_contents populates the local variable $http_response_header
        // This is not a great idea, but it is what it is.
        // For more info: http://php.net/manual/es/reserved.variables.httpresponseheader.php
        if ($contents === false) {
            $headers = implode("\n", $http_response_header);
            $error = "Failed to open $url";

            if (preg_match('/^\s*HTTP(\/[\d\.]+)?\s+([45]\d{2})\s+(.*)/mi', $headers, $matches)) {
                $statusMessage = trim($matches[0]);
                $errorCode = $matches[2];
                $errorMessage = $matches[3];

                $error = $errorCode == 404
                    ? "Url not found: $url"
                    : "Failed to open $url:\n" . $statusMessage;
            }

            throw new HttpRequestException($error);
        }

        return $contents;
    }
}
