<?php

declare(strict_types=1);

namespace EdmondsCommerce\Testing\Assertions;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\TestFramework\Helper\Bootstrap;

class ModelAssertions
{
    public function __construct(
        private readonly DatabaseAssertions $databaseAssertions
    ) {
    }

    /**
     * @throws LocalizedException
     */
    public function assertExists(AbstractModel $model, string $message = null): void
    {
        $resource = $model->getResource();
        $this->databaseAssertions->assertRowExists(
            $resource->getMainTable(),
            [$resource->getIdFieldName() => $model->getData($resource->getIdFieldName())],
            $message ?? 'Model %s with ' . $resource->getIdFieldName() . ' value ' . $model->getId() . ' does not exist'
        );
    }

    /**
     * @throws LocalizedException
     */
    public function assertNotExists(AbstractModel $model, string $message = null): void
    {
        $resource = $model->getResource();
        $this->databaseAssertions->assertRowNotExist(
            $resource->getMainTable(),
            [$resource->getIdFieldName() => $model->getData($resource->getIdFieldName())],
            $message ?? 'Model %s with ' . $resource->getIdFieldName() . ' value ' . $model->getId() . ' does not exist'
        );
    }

    /**
     * @param string $model
     *
     * @throws LocalizedException
     */
    public function assertModelCount(string $modelClassName, int $expectedCount, string $message = null): void
    {
        $resource = $this->getResourceModelForModel($modelClassName);

        $this->databaseAssertions->assertTotalTableRowCount(
            $resource->getMainTable(),
            $expectedCount,
            $message ?? sprintf(
                'Incorrect %s count in database, expected %s but found %s',
                $modelClassName,
                $expectedCount,
                $message
            )
        );
    }

    private function getResourceModelForModel(string|AbstractModel $model)
    {
        if (!is_string($model)) {
            return $model->getResource();
        }

        /** @var AbstractModel $model */
        $model = Bootstrap::getObjectManager()->create($model);

        return $model->getResource();
    }
}