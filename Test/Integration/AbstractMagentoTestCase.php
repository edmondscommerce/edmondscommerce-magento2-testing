<?php

declare(strict_types=1);

namespace EdmondsCommerce\Testing\Test\Integration;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreRepository;
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

    public function getProductBySku(
        string $sku,
        bool $editMode = false,
        string $storeCode = null,
        bool $forceReload = false
    ): ProductInterface {
        /** @var ProductRepositoryInterface $repository */
        $repository = $this->getObjectManager()->create(ProductRepositoryInterface::class);
        $storeId = null;
        if($storeCode !== null) {
            $storeId = $this->getObjectManager()->get(StoreRepository::class)->get($storeCode)->getId();
        }
        $product    = $repository->get($sku, $editMode, $storeId, $forceReload);
        if (!$product instanceof ProductInterface) {
            throw new RuntimeException('Expected ProductInterface, got ' . get_class($product));
        }

        return $product;
    }

    protected function assertProductHasChanges(ProductInterface $product): void
    {
        if (!$product instanceof AbstractModel) {
            throw new RuntimeException('Product must extend AbstractModel, got ' . get_class($product));
        }
        self::assertTrue($product->hasDataChanges(), 'No changes detected in the products');
    }

    protected function assertProductHasNoChanges(ProductInterface $product): void
    {
        if (!$product instanceof AbstractModel) {
            throw new RuntimeException('Product must extend AbstractModel, got ' . get_class($product));
        }
        self::assertFalse($product->hasDataChanges(), 'Changes detected in the products');
    }
}
