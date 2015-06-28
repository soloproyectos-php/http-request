<?php
/**
 * This file is part of SoloProyectos common library.
 *
 * @author  Gonzalo Chumillas <gchumillas@email.com>
 * @license https://github.com/soloproyectos-php/http/blob/master/LICENSE The MIT License (MIT)
 * @link    https://github.com/soloproyectos-php/http
 */
namespace soloproyectos\http\request;

/**
 * Class HttpRequestFormData.
 *
 * @package Http
 * @author  Gonzalo Chumillas <gchumillas@email.com>
 * @license https://github.com/soloproyectos-php/http/blob/master/LICENSE The MIT License (MIT)
 * @link    https://github.com/soloproyectos-php/http
 */
class HttpRequestFormData
{
    /**
     * MIME type.
     * @var string
     */
    private $_mimeType = "";

    /**
     * Filename.
     * @var string
     */
    private $_filename = "";

    /**
     * Data.
     * @var scalar|scalar[]
     */
    private $_data = "";

    /**
     * Constructor.
     *
     * Example:
     * ```php
     * $contents = file_get_contents("/path/to/image.jpg");
     * $data = new HttpRequestFormData($contents, "image/jpeg");
     * ```
     *
     * @param scalar|scalar[] $data     Data
     * @param string          $mimeType MIME type (not required)
     * @param string          $filename Filename (not required)
     */
    public function __construct($data, $mimeType = "", $filename = "")
    {
        $this->_data = $data;
        $this->_mimeType = $mimeType;
        $this->_filename = $filename;
    }

    /**
     * Gets the MIME type.
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->_mimeType;
    }

    /**
     * Sets the MIME type.
     *
     * @param string $value MIME type
     *
     * @return void
     */
    public function setMimeType($value)
    {
        $this->_mimeType = $value;
    }

    /**
     * Gets the filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->_filename;
    }

    /**
     * Sets the filename.
     *
     * @param string $value Filename
     *
     * @return void
     */
    public function setFilename($value)
    {
        $this->_filename = $value;
    }

    /**
     * Gets data.
     *
     * @return string
     */
    public function getData()
    {
        return $this->_data;
    }
}
