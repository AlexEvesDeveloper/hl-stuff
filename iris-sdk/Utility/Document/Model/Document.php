<?php

namespace Barbondev\IRISSDK\Utility\Document\Model;

use Barbondev\IRISSDK\Common\Model\AbstractStreamResponseModel;
use Guzzle\Service\Command\OperationCommand;
use Guzzle\Stream\Stream;

/**
 * Class Document
 *
 * @package Barbondev\IRISSDK\Utility\Document\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class Document extends AbstractStreamResponseModel
{
    /**
     * @var string
     */
    private $data;

    /**
     * {@inheritdoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        $instance = new self();

        $response = $command->getResponse();
        $body = $response->getBody();

        if ($body instanceof Stream) {

            $contentDispositionParts = self::parseContentDisposition($response->getContentDisposition());

            $instance
                ->setStream($body)
                ->setSize($response->getBody()->getSize())
                ->setMimeType($response->getContentType())
                ->setFileExtension($contentDispositionParts['extension'])
                ->setFileName($contentDispositionParts['filename'])
                ->setData($response->getBody(true))
            ;
        }

        return $instance;
    }

    /**
     * Set data
     *
     * @param string $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }
}