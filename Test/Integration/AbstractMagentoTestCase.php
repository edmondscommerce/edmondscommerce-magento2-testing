<?php

declare(strict_types=1);

namespace EdmondsCommerce\Testing\Test\Integration;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreRepository;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use RuntimeException;
use function array_pop;
use function get_class;

abstract class AbstractMagentoTestCase extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectMaanger;

    public function setUp(): void
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
        $storeId    = null;
        if ($storeCode !== null) {
            $storeId = $this->getObjectManager()->get(StoreRepository::class)->get($storeCode)->getId();
        }
        $product = $repository->get($sku, $editMode, $storeId, $forceReload);
        if (!$product instanceof ProductInterface) {
            throw new RuntimeException('Expected ProductInterface, got ' . get_class($product));
        }

        return $product;
    }

    public function getCustomerByEmail(string $email, ?int $websiteId = null): CustomerInterface
    {
        /** @var CustomerRepository $repository */
        $repository = $this->getObjectManager()->create(CustomerRepository::class);

        return $repository->get($email, $websiteId);
    }

    public function getCustomerGroupByCode(string $groupCode): GroupInterface
    {
        /** @var GroupRepositoryInterface $repository */
        $repository = $this->getObjectManager()->get(GroupRepositoryInterface::class);
        /** @var SearchCriteriaBuilderFactory $criteriaBuilder */
        $criteriaBuilder = $this->getObjectManager()->get(SearchCriteriaBuilderFactory::class);
        $criteria = $criteriaBuilder->create()->addFilter('customer_group_code', $groupCode)->create();
        $groups = $repository->getList($criteria);
        $count = $groups->getTotalCount();
        if($count !== 1) {
            throw new RuntimeException("Did not find exactly one group with code $groupCode");
        }
        
        $allGroups =  $groups->getItems();

        return array_pop($allGroups);
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

    /**
     * If you are caching results in a class property, then these are persisted between tests. For testing that class,
     * you can use the object manager create functionality to get a new instance for each test.
     *
     * However, if you are testing a class that uses that class, then this becomes more difficult. As I do this a lot,
     * this is a simple helper method to allow these values to be set to null allowing different configuration to be
     * tested
     *
     * @param object $class
     * @param string $property
     *
     * @throws \ReflectionException
     */
    protected function setClassVariableToNull($class, string $property): void
    {
        $reflectionProperty = new ReflectionProperty($class, $property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($class, null);
    }
    
    protected function replaceClassWithPreference(string $classToBeReplacedFQN, string $newClassFQN): void
    {
        $this->getObjectManager()->configure(
            [
                'preferences' => [
                    $classToBeReplacedFQN => $newClassFQN,
                ],
            ]
        );
    }
}
