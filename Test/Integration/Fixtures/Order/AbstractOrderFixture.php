<?php

declare(strict_types=1);

namespace EdmondsCommerce\Testing\Test\Integration\Fixtures\Order;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address as OrderAddress;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Sales\Model\Order\Payment;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Store\Model\StoreManagerInterface;
use RuntimeException;
use function array_merge;
use function in_array;

abstract class AbstractOrderFixture
{
    abstract public static function createOrderFixture(): void;

    protected static function createBillingAddress(array $details = []): OrderAddress
    {
        return self::createDefaultAddress('billing', $details);
    }

    protected static function createShippingAddress(array $details = []): OrderAddress
    {
        return self::createDefaultAddress('shipping', $details);
    }

    protected static function createPaymentDetails(): Payment
    {
        /** @var Payment $payment */
        $payment = self::getObjectManager()->create(Payment::class);
        $payment->setMethod('checkmo')
                ->setAdditionalInformation('last_trans_id', '11122')
                ->setAdditionalInformation(
                    'metadata',
                    [
                        'type'       => 'free',
                        'fraudulent' => false,
                    ]
                );

        return $payment;
    }

    protected static function createOrderItem(string $sku, int $quantity = 1): OrderItem
    {
        $product = self::getProductFromSku($sku);
        /** @var OrderItem $orderItem */
        $orderItem = self::getObjectManager()->create(OrderItem::class);
        $orderItem->setProductId($product->getId())
                  ->setQtyOrdered($quantity)
                  ->setBasePrice($product->getPrice())
                  ->setPrice($product->getPrice())
                  ->setRowTotal($quantity * $product->getPrice())
                  ->setProductType($product->getTypeId())
                  ->setName($product->getName());

        return $orderItem;
    }

    protected static function createOrder(
        string $incrementId,
        string $customerEmail,
        string $state = Order::STATE_PROCESSING
    ): Order {
        /** @var Order $order */
        $order = self::getObjectManager()->create(Order::class);
        $order->setIncrementId($incrementId)
              ->setState($state)
              ->setStatus($order->getConfig()->getStateDefaultStatus($state))
              ->setSubtotal(100)
              ->setGrandTotal(100)
              ->setBaseSubtotal(100)
              ->setBaseGrandTotal(100)
              ->setStoreId(self::getObjectManager()->get(StoreManagerInterface::class)->getStore()->getId())
              ->setCustomerIsGuest(true)
              ->setCustomerEmail($customerEmail);

        return $order;
    }

    protected static function saveOrder(Order $order): void
    {
        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = self::getObjectManager()->create(OrderRepositoryInterface::class);
        $orderRepository->save($order);
    }

    protected static function getProductFromSku(string $sku): ProductInterface
    {
        /** @var ProductRepositoryInterface $repository */
        $repository = self::getObjectManager()->create(ProductRepositoryInterface::class);

        return $repository->get($sku);
    }

    protected static function getObjectManager(): ObjectManagerInterface
    {
        return Bootstrap::getObjectManager();
    }

    private static function createDefaultAddress(string $type, array $details = []): OrderAddress
    {
        $defaultAddressDetails = [
            'region'     => 'CA',
            'region_id'  => '12',
            'postcode'   => '11111',
            'lastname'   => 'lastname',
            'firstname'  => 'firstname',
            'street'     => 'street',
            'city'       => 'Los Angeles',
            'email'      => 'admin@example.com',
            'telephone'  => '11111111',
            'country_id' => 'US',
        ];

        $addressData = array_merge($defaultAddressDetails, $details);

        /** @var OrderAddress $address */
        $address = self::getObjectManager()->create(OrderAddress::class, ['data' => $addressData]);
        if (!in_array($type, ['billing', 'shipping'])) {
            throw new RuntimeException('Unknown Address type');
        }
        $address->setAddressType($type);

        return $address;
    }
}
