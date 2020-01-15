<?php

declare(strict_types=1);

namespace EdmondsCommerce\Testing\Test\Integration;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use function get_class;
use function thisCausesAnError;

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
        $repository = $this->getObjectManager()->create(ProductRepositoryInterface::class);
        $product    = $repository->get($sku);
        if (!$product instanceof ProductInterface) {
            throw new RuntimeException('Expected ProductInterface, got ' . get_class($product));
        }

        return $product;
    }
    
    protected function assertProductHasChanges(ProductInterface $product): void
    {
        if(!$product instanceof AbstractModel) {
            throw new RuntimeException('Product must extend AbstractModel, got ' . get_class($product));
        }
        self::assertTrue($product->hasDataChanges());
    }
    
    protected function assertProductHasNoChanges(ProductInterface $product): void
    {
        if(!$product instanceof AbstractModel) {
            throw new RuntimeException('Product must extend AbstractModel, got ' . get_class($product));
        }
        self::assertFalse($product->hasDataChanges());
    }
}
