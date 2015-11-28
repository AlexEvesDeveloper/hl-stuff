<?php

namespace Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model;

use Barbondev\IRISSDK\Common\Model\AbstractResponseModel;
use Guzzle\Common\Collection;
use Guzzle\Service\Command\OperationCommand;

/**
 * Class Document
 *
 * @package Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class Document extends AbstractResponseModel
{
    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $category;

    /**
     * @var string
     */
    private $nodeId;

    /**
     * @var string
     */
    private $name;

    /**
     * {@inheritdoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        $data = $command->getResponse()->json();

        // Collection of documents
        if (self::isResponseDataIndexedArray($data)) {

            $documents = new Collection();

            foreach ($data as $key => $documentData) {
                $documents->add($key, self::hydrateModelProperties(
                    new self(),
                    $documentData
                ));
            }

            return $documents;
        }

        // Single document
        else {
            return self::hydrateModelProperties(
                new self(),
                $data
            );
        }
    }

    /**
     * Set category
     *
     * @param string $category
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set nodeId
     *
     * @param string $nodeId
     * @return $this
     */
    public function setNodeId($nodeId)
    {
        $this->nodeId = $nodeId;
        return $this;
    }

    /**
     * Get nodeId
     *
     * @return string
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }
}