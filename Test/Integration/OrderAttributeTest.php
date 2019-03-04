<?php

namespace TddWizard\ExerciseBrands\Test\Integration;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use TddWizard\Fixtures\Catalog\ProductBuilder;
use TddWizard\Fixtures\Catalog\ProductFixture;
use TddWizard\Fixtures\Catalog\ProductFixtureRollback;
use TddWizard\Fixtures\Checkout\CartBuilder;
use TddWizard\Fixtures\Checkout\CustomerCheckout;
use TddWizard\Fixtures\Customer\AddressBuilder;
use TddWizard\Fixtures\Customer\CustomerBuilder;
use TddWizard\Fixtures\Customer\CustomerFixture;
use TddWizard\Fixtures\Customer\CustomerFixtureRollback;

/**
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 */
class OrderAttributeTest extends TestCase
{
    /**
     * @var ProductFixture
     */
    public $productFixture;
    /**
     * @var ObjectManager
     */
    private $objectManager;
    /**
     * @var CustomerFixture
     */
    private $customerFixture;

    protected function setUp()
    {
        $this->productFixture = new ProductFixture(
            ProductBuilder::aSimpleProduct()->withCustomAttributes(['brand' => 'Nike'])->build()
        );
        $this->customerFixture = new CustomerFixture(
            CustomerBuilder::aCustomer()->withAddresses(
                AddressBuilder::anAddress()->asDefaultBilling()->asDefaultShipping()
            )->build()
        );
        $this->objectManager = Bootstrap::getObjectManager();
    }

    protected function tearDown()
    {
        ProductFixtureRollback::create()->execute($this->productFixture);
        CustomerFixtureRollback::create()->execute($this->customerFixture);
    }

    public function testBrandAttributeIsSavedInOrderItem()
    {
        $this->customerFixture->login();
        $cart = CartBuilder::forCurrentSession()->withSimpleProduct($this->productFixture->getSku())->build();
        $order = CustomerCheckout::fromCart($cart)->placeOrder();
        /** @var Order\Item $orderItem */
        $orderItem = array_values($order->getItems())[0];
        $this->assertEquals('Nike', $orderItem->getExtensionAttributes()->getBrand());
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testBrandIsAvailableInExtensionAttributes()
    {
        $item = $this->getAnyOrderItem();

        $brandValue = 'Adidas';
        $item->getExtensionAttributes()->setBrand($brandValue);
        $this->assertEquals(
            $brandValue,
            $item->getExtensionAttributes()->getBrand(),
            'Generated getter and setter should work'
        );
        /** @var OrderItemRepositoryInterface $itemRepository */
        $itemRepository = $this->objectManager->create(OrderItemRepositoryInterface::class);
        $itemRepository->save($item);
        $this->assertEquals($brandValue, $item->getData('brand'), 'Internal data should be set after save');
        $itemRepository = $this->objectManager->create(OrderItemRepositoryInterface::class);
        $loadedItem = $itemRepository->get($item->getItemId());
        $this->assertEquals($brandValue, $loadedItem->getData('brand'), 'Internal data should be set after load');
        $this->assertEquals(
            $brandValue,
            $loadedItem->getExtensionAttributes()->getBrand(),
            'Extension attribute should be set after load'
        );
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->objectManager->create(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter('item_id', $item->getItemId());
        $itemFromList = array_values($itemRepository->getList($searchCriteriaBuilder->create())->getItems())[0];
        $this->assertEquals($brandValue, $itemFromList->getData('brand'), 'Internal data should be set after getList');
        $this->assertEquals(
            $brandValue,
            $itemFromList->getExtensionAttributes()->getBrand(),
            'Extension attribute should be set after getList'
        );
    }

    private function getAnyOrderItem(): OrderItemInterface
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->objectManager->create(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->setPageSize(1);
        /** @var OrderItemRepositoryInterface $itemRepository */
        $itemRepository = $this->objectManager->create(OrderItemRepositoryInterface::class);
        return array_values($itemRepository->getList($searchCriteriaBuilder->create())->getItems())[0];
    }
}
