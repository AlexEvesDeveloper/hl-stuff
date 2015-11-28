<?php

namespace Barbondev\IRISSDK\Common\Model;

use Barbondev\IRISSDK\Common\Model\AbstractResponseModel;
use Guzzle\Stream\Stream;

/**
 * Class AbstractStreamResponseModel
 *
 * @package Barbondev\IRISSDK\Common\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
abstract class AbstractStreamResponseModel extends AbstractResponseModel
{
    /**
     * @var Stream
     */
    protected $stream;

    /**
     * @var string
     */
    protected $mimeType;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var string
     */
    protected $fileExtension;

    /**
     * Set fileExtension
     *
     * @param string $fileExtension
     * @return $this
     */
    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;
        return $this;
    }

    /**
     * Get fileExtension
     *
     * @return string
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set mimeType
     *
     * @param string $mimeType
     * @return $this
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set size
     *
     * @param int $size
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Get size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set stream
     *
     * @param \Guzzle\Stream\Stream $stream
     * @return $this
     */
    public function setStream($stream)
    {
        $this->stream = $stream;
        return $this;
    }

    /**
     * Get stream
     *
     * @return \Guzzle\Stream\Stream
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Parse the content disposition header, returning an array of elements
     *
     * <code>
     * Array {
     *     type: attachment
     *     filename: dms1965305261398562239.jpg
     *     extension: .jpg
     * }
     * </code>
     *
     * @param string $contentDisposition
     * @throws \InvalidArgumentException
     * @return array
     */
    public static function parseContentDisposition($contentDisposition)
    {
        if (preg_match('/([a-z]+);\W?filename=\"(.*)\"/', $contentDisposition, $matches)) {

            return array(
                'type' => isset($matches[1]) ? $matches[1] : null,
                'filename' => isset($matches[2]) ? $matches[2] : null,
                'extension' => pathinfo(isset($matches[2]) ? $matches[2] : null, PATHINFO_EXTENSION),
            );
        }

        throw new \InvalidArgumentException(sprintf('Content disposition "%s" is invalid', $contentDisposition));
    }
}