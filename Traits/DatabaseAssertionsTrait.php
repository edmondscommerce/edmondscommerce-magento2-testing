<?php

declare(strict_types=1);

namespace Traits;

use EdmondsCommerce\Testing\Assertions\DatabaseAssertions;
use Magento\TestFramework\Helper\Bootstrap;

trait DatabaseAssertionsTrait
{
    private ?DatabaseAssertions $databaseAssert = null;

    public function assertDatabase(): DatabaseAssertions
    {
        if ($this->databaseAssert === null) {
            $objectManager        = Bootstrap::getObjectManager();
            $this->databaseAssert = $objectManager->get(DatabaseAssertions::class);
        }

        return $this->databaseAssert;
    }
}