<?php

namespace Barbondev\IRISSDK\SystemApplication\SystemApplication\Model;

use Guzzle\Common\Collection;
use Barbondev\IRISSDK\Common\Model\AbstractResponseModel;
use Guzzle\Service\Command\OperationCommand;

/**
 * Class ReferencingApplicationFindResults
 *
 * @package Barbondev\IRISSDK\SystemApplication\SystemApplication\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class ReferencingApplicationFindResults extends AbstractResponseModel
{
    /**
     * @var Collection
     */
    private $records;

    /**
     * @var int
     */
    private $totalRecords;

    /**
     * {@inheritdoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        $data = $command->getResponse()->json();

        $records = new Collection();

        if (isset($data['records']) && is_array($data['records'])) {
            foreach ($data['records'] as $key => $object) {
                $records->add(
                    $key,
                    self::hydrateModelProperties(
                        new ReferencingApplicationFindResult(),
                        $object,
                        array(
                            'applicationUuid' => 'referencingApplicationUuId',
                        )
                    )
                );
            }
        }

        $instance = self::hydrateModelProperties(
            new self(),
            $data,
            array(),
            array(
                'records' => $records,
            )
        );

        return $instance;
    }

    /**
     * Set records
     *
     * @param \Guzzle\Common\Collection $records
     * @return $this
     */
    public function setRecords(Collection $records)
    {
        $this->records = $records;
        return $this;
    }

    /**
     * Get records
     *
     * @return \Guzzle\Common\Collection
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * Set totalRecords
     *
     * @param int $totalRecords
     * @return $this
     */
    public function setTotalRecords($totalRecords)
    {
        $this->totalRecords = $totalRecords;
        return $this;
    }

    /**
     * Get totalRecords
     *
     * @return int
     */
    public function getTotalRecords()
    {
        return $this->totalRecords;
    }
}