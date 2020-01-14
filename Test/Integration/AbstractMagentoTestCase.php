<?php

declare(strict_types=1);

namespace EdmondsCommerce\Testing\Test\Integration;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use function get_class;

abstract class AbstractMagentoTestCase extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectMaanger;

    public function setUp()
    {
        parent::setUp();
        $this->objectMaanger = Bootstrap::getObjectManager();
    }

    public function getObjectManager(): ObjectManagerInterface
    {
        return $this->objectMaanger;
    }

    public function getProductBySku(string $sku): ProductInterface
    {
        /** @var ProductRepositoryInterface $repository */
        $repository = $this->getObjectManager()->get(ProductRepositoryInterface::class);
        $product    = $repository->get($sku);
        if (!$product instanceof ProductInterface) {
            throw new RuntimeException('Expected ProductInterface, got ' . get_class($product));
        }

        return $product;
    }
}
