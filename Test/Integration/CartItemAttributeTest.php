<?php

namespace TddWizard\ExerciseBrands\Test\Integration;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\App\ObjectManager;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use TddWizard\Fixtures\Catalog\ProductBuilder;
use TddWizard\Fixtures\Catalog\ProductFixture;
use TddWizard\Fixtures\Catalog\ProductFixtureRollback;
use TddWizard\Fixtures\Checkout\CartBuilder;
use TddWizard\Fixtures\Customer\AddressBuilder;
use TddWizard\Fixtures\Customer\CustomerBuilder;
use TddWizard\Fixtures\Customer\CustomerFixture;
use TddWizard\Fixtures\Customer\CustomerFixtureRollback;

/**
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 */
class CartItemAttributeTest extends TestCase
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

    public function testBrandAttributeIsSavedInQuoteItem()
    {
        $this->customerFixture->login();
        $cart = CartBuilder::forCurrentSession()->withSimpleProduct($this->productFixture->getSku())->build();
        /** @var Quote\Item $quoteItem */
        $quoteItem = $cart->getQuote()->getAllItems()[0];
        $this->assertEquals('Nike', $quoteItem->getData('brand'));
        $quoteItem->load($quoteItem->getItemId());
        /** @var CartRepositoryInterface $quoteRepository */
        $quoteRepository = $this->objectManager->create(CartRepositoryInterface::class);
        $loadedItem = $quoteRepository->get($cart->getQuote()->getId())->getItems()[0];
        $this->assertEquals('Nike', $loadedItem->getData('brand'), 'getData');
//        $reloadedItem = $loadedItem->load($loadedItem->getItemId());
        $this->assertEquals('Nike', $loadedItem->getExtensionAttributes()->getBrand(), 'getExtensionAttributes');
    }

    /**
     * @magentoDataFixture Magento/Checkout/_files/quote_with_simple_product.php
     */
    public function testBrandIsAvailableInExtensionAttributes()
    {
        $item = $this->getQuoteItem();

        $brandValue = 'Adidas';
        $item->getExtensionAttributes()->setBrand($brandValue);
        $this->assertEquals(
            $brandValue,
            $item->getExtensionAttributes()->getBrand(),
            'Generated getter and setter should work'
        );
        $this->markTestIncomplete('cart item repository does not allow arbitrary changes?');
        /** @var CartItemRepositoryInterface $itemRepository */
        $itemRepository = $this->objectManager->create(CartItemRepositoryInterface::class);
        $itemRepository->save($item);
        $this->assertEquals($brandValue, $item->getData('brand'), 'Internal data should be set after save');
        $itemRepository = $this->objectManager->create(CartItemRepositoryInterface::class);
        $loadedItem = array_values($itemRepository->getList($item->getQuoteId()))[0];
        $this->assertEquals($item->getItemId(), $loadedItem->getItemid());
        $loadedItem->load($loadedItem->getItemId());
        $this->assertEquals($brandValue, $loadedItem->getData('brand'), 'Internal data should be set after load');
        $this->assertEquals(
            $brandValue,
            $loadedItem->getExtensionAttributes()->getBrand(),
            'Extension attribute should be set after load'
        );
    }

    private function getQuoteItem(): CartItemInterface
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->objectManager->create(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->setPageSize(1);
        $searchCriteriaBuilder->addSortOrder(new SortOrder(
            [
                SortOrder::FIELD => 'entity_id',
                SortOrder::DIRECTION => SortOrder::SORT_DESC
            ]
        ));
        /** @var CartRepositoryInterface $quoteRepository */
        $quoteRepository = $this->objectManager->create(CartRepositoryInterface::class);
        /** @var CartInterface $quote */
        $quote = array_values($quoteRepository->getList($searchCriteriaBuilder->create())->getItems())[0];
        return array_values($quote->getItems())[0];
    }
}
