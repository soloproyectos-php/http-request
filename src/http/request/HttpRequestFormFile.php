<?php
/**
 * This file is part of SoloProyectos common library.
 *
 * @author  Gonzalo Chumillas <gchumillas@email.com>
 * @license https://github.com/soloproyectos-php/http/blob/master/LICENSE The MIT License (MIT)
 * @link    https://github.com/soloproyectos-php/http
 */
namespace soloproyectos\http\request;
use \Finfo;
use soloproyectos\http\request\exception\HttpRequestException;
use soloproyectos\text\Text;

/**
 * Class HttpRequestFormFile.
 *
 * @package Http
 * @author  Gonzalo Chumillas <gchumillas@email.com>
 * @license https://github.com/soloproyectos-php/http/blob/master/LICENSE The MIT License (MIT)
 * @link    https://github.com/soloproyectos-php/http
 */
class HttpRequestFormFile extends HttpRequestFormData
{
    /**
     * Constructor.
     *
     * @param string $path     Path to file
     * @param string $mimeType MIME type (not required)
     * @param string $filename Filename (not required)
     */
    public function __construct($path, $mimeType = "", $filename = "")
    {
        $contents = file_get_contents($path);
        parent::__construct($contents);

        // sets mimetype
        if (Text::isEmpty($mimeType)) {
            $result = new Finfo(FILEINFO_MIME);
            $mimeType = $result->buffer($contents);
            if ($mimeType === false) {
                throw new HttpRequestException("Error detecting MIME type");
            }
        }
        $this->setMimeType($mimeType);

        // sets filename
        if (Text::isEmpty($filename)) {
            $filename = basename($path);
        }
        $this->setFilename($filename);
    }
}
