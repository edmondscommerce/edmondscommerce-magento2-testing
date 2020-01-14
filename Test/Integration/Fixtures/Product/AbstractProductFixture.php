<?php

declare(strict_types=1);

namespace EdmondsCommerce\Testing\Test\Integration\Fixtures\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

abstract class AbstractProductFixture
{
    abstract public static function createProductFixture();
    
    protected static function setBasicProductDetails(Product $product): Product
    {
        $product
            ->setTypeId('simple')
            ->setAttributeSetId(4)
            ->setWebsiteIds([1])
            ->setVisibility(Visibility::VISIBILITY_BOTH)
            ->setStatus(Status::STATUS_ENABLED)
            ->setStockData(['use_config_manage_stock' => 0]);

        return $product;
    }

    protected static function createProduct(int $productId, string $sku, string $name, float $price): Product
    {
        $objectManager = self::getObjectManager();
        /** @var Product $product */
        $product = $objectManager->create(Product::class);
        $product
            ->setId($productId)
            ->setSku($sku)
            ->setName($name)
            ->setPrice($price);

        return $product;
    }

    protected static function saveProduct(Product $product): void
    {
        $objectManager = self::getObjectManager();
        /** @var ProductRepositoryInterface $respository */
        $respository = $objectManager->get(ProductRepositoryInterface::class);
        $respository->save($product);
    }

    protected static function getObjectManager(): ObjectManagerInterface
    {
        return Bootstrap::getObjectManager();
    }
}
