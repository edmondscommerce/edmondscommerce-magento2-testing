<?php

declare(strict_types=1);

namespace EdmondsCommerce\Testing\Traits;

use EdmondsCommerce\Testing\Assertions\DatabaseAssertions;
use EdmondsCommerce\Testing\Assertions\ModelAssertions;
use Magento\TestFramework\Helper\Bootstrap;

trait ModelAssertionsTrait
{
    private ?ModelAssertions $modelAssertions = null;

    public function assertModel(): ModelAssertions
    {
        if ($this->modelAssertions === null) {
            $objectManager         = Bootstrap::getObjectManager();
            $this->modelAssertions = $objectManager->get(ModelAssertions::class);
        }

        return $this->modelAssertions;
    }
}