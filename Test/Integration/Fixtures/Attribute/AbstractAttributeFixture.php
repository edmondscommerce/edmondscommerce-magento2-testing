<?php

declare(strict_types=1);

namespace EdmondsCommerce\Testing\Test\Integration\Fixtures\Attribute;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Setup\CategorySetup;
use Magento\TestFramework\Helper\Bootstrap;

abstract class AbstractAttributeFixture
{
    abstract public static function createAttributeFixture(): void;

    protected static function createProductAttribute(string $code): Attribute
    {
        /** @var Attribute $attribute */
        $attribute = Bootstrap::getObjectManager()->create(Attribute::class);
        $attribute->setAttributeCode($code)
                  ->setEntityTypeId(4)
                  ->setBackendType('varchar');

        return $attribute;
    }

    protected static function saveProductAttribute(
        Attribute $attribute,
        string $set = 'Default',
        string $group = 'General'
    ): void {
        /** @var CategorySetup $installer */
        $installer = Bootstrap::getObjectManager()->create(CategorySetup::class);
        $attribute->save();
        $installer->addAttributeToGroup('catalog_product', $set, $group, $attribute->getId());
    }
}
