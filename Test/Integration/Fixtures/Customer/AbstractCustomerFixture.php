<?php

declare(strict_types=1);

namespace EdmondsCommerce\Testing\Test\Integration\Fixtures\Customer;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Data\Address;
use Magento\Customer\Model\Data\Customer;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Customer\Model\ResourceModel\GroupRepository;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

abstract class AbstractCustomerFixture
{
    abstract public static function createCustomerFixture(): void;

    protected static function createBasicCustomer(
        string $customerEmail,
        int $websiteId = 1,
        int $storeId = 1,
        int $groupId = 1
    ): CustomerInterface {
        /** @var Customer $customer */
        $customer = self::getObjectManager()->create(Customer::class);
        $customer
            ->setEmail($customerEmail)
            ->setWebsiteId($websiteId)
            ->setStoreId($storeId)
            ->setGroupId($groupId)
            ->setFirstname('Test')
            ->setLastname('Customer');

        return $customer;
    }

    protected static function createAddress(): AddressInterface
    {
        /** @var Address $address */
        $address = self::getObjectManager()->create(Address::class);
        $address
            ->setFirstname('Test')
            ->setLastname('Customer')
            ->setStreet(['street 1', 'street 2'])
            ->setCity('Test City')
            ->setRegionId(10)
            ->setCountryId('US')
            ->setPostcode('01001')
            ->setTelephone('+7000000001');

        return $address;
    }

    protected static function saveCustomer(CustomerInterface $customer): void
    {
        /** @var CustomerRepository $repository */
        $repository = self::getObjectManager()->create(CustomerRepository::class);
        /** @var CustomerRegistry $registry */
        $registry = self::getObjectManager()->get(CustomerRegistry::class);
        $repository->save($customer);
        $registry->remove($customer->getId());
    }

    protected static function createGroup(string $code): GroupInterface
    {
        /** @var GroupInterface $group */
        $group = self::getObjectManager()->create(GroupInterface::class);
        $group->setCode($code);
        $group->setTaxClassId(GroupRepository::DEFAULT_TAX_CLASS_ID);

        return $group;
    }

    protected static function saveGroup(GroupInterface $group): GroupInterface
    {
        /** @var GroupRepository $repository */
        $repository = self::getObjectManager()->create(GroupRepository::class);
        return $repository->save($group);
    }

    protected static function getObjectManager(): ObjectManagerInterface
    {
        return Bootstrap::getObjectManager();
    }
}
