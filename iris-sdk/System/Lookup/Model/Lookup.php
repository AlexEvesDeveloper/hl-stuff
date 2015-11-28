<?php

namespace Barbondev\IRISSDK\System\Lookup\Model;

use Barbondev\IRISSDK\Common\Model\AbstractResponseModel;
use Guzzle\Service\Command\OperationCommand;
use Guzzle\Common\Collection;

/**
 * Class Lookup
 *
 * @package Barbondev\IRISSDK\System\Lookup\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class Lookup extends AbstractResponseModel
{
    /**
     * @var Collection of LookupCategory
     */
    private $categories;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new Collection();
    }

    /**
     * {@inheritdoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        $instance = new self();

        foreach ($command->getResponse()->json() as $categoryName => $items) {

            $lookupCategory = new LookupCategory();
            $lookupCategory->setName($categoryName);

            foreach ($items as $item) {

                $lookupItem = new LookupItem();

                $lookupItem
                    ->setId($item['index'])
                    ->setName($item['value'])
                ;

                $lookupCategory->addItem($lookupItem);
            }

            $instance->addCategory($lookupCategory);
        }

        return $instance;
    }

    /**
     * Set categories
     *
     * @param \Guzzle\Common\Collection $categories
     * @return $this
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * Get categories
     *
     * @return \Guzzle\Common\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add category
     *
     * @param LookupCategory $category
     */
    public function addCategory(LookupCategory $category)
    {
        $this->categories->add($category->getName(), $category);
    }

    /**
     * Get a category by name
     *
     * @param string $name
     * @return LookupCategory
     * @throws \InvalidArgumentException
     */
    public function getCategoryByName($name)
    {
        if ($this->categories->hasKey($name)) {
            return $this->categories->get($name);
        }

        throw new \InvalidArgumentException(sprintf('Lookup category with name %s does not exist', $name));
    }
}