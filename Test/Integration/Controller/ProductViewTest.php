<?php

namespace TddWizard\ExerciseBrands\Test\Integration\Controller;

use Magento\TestFramework\TestCase\AbstractController;
use TddWizard\Fixtures\Catalog\ProductBuilder;
use TddWizard\Fixtures\Catalog\ProductFixture;
use TddWizard\Fixtures\Catalog\ProductFixtureRollback;

/**
 * @magentoAppArea frontend
 */
class ProductViewTest extends AbstractController
{
    const XPATH_BRAND_LIST = '//div[@id="exercise_brands_list"]';
    /**
     * @var ProductFixture[]
     */
    private $productFixtures;

    protected function setUp()
    {
        parent::setUp();
        $this->productFixtures = [
            $this->createProductFixture('Adiletten', 'adidas'),
            $this->createProductFixture('Nike Air', 'nike'),
            $this->createProductFixture('Nike Earth', 'nike'),
            $this->createProductFixture('Nike Fire', 'nike'),
            $this->createProductFixture('Nike Water', 'nike'),
        ];
    }

    protected function tearDown()
    {
        ProductFixtureRollback::create()->execute(...$this->productFixtures);
    }

    /**
     * @param $name
     * @param $brand
     * @return ProductFixture
     */
    protected function createProductFixture($name, $brand): ProductFixture
    {
        return new ProductFixture(
            ProductBuilder::aSimpleProduct()->withName($name)->withCustomAttributes(['brand' => $brand])->build()
        );
    }

    public function testBrandListOnProductPage()
    {
        $this->dispatch('catalog/product/view/id/' . $this->productFixtures[1]->getId());
        $this->assertDomElementPresent(self::XPATH_BRAND_LIST, 'Brand list div should be present');
        $this->assertDomElementContains(self::XPATH_BRAND_LIST, 'Other products of nike');
        $this->assertDomElementCount(
            '//div[@id="exercise_brands_list"]/ul/li', 3, 'All other products of same brand should be listed'
        );
        $this->assertDomElementContains(self::XPATH_BRAND_LIST, 'Nike Earth');
        $this->assertDomElementContains(self::XPATH_BRAND_LIST, 'Nike Fire');
        $this->assertDomElementContains(self::XPATH_BRAND_LIST, 'Nike Water');
        $this->assertDomElementContains(self::XPATH_BRAND_LIST, $this->productFixtures[2]->getSku());
        $this->assertDomElementContains(self::XPATH_BRAND_LIST, $this->productFixtures[3]->getSku());
        $this->assertDomElementContains(self::XPATH_BRAND_LIST, $this->productFixtures[4]->getSku());
    }

    private function assertDomElementPresent(string $xpath, string $message = '')
    {
        $this->assertDomElementCount($xpath, 1, $message);
    }

    private function assertDomElementCount(string $xpath, int $expectedCount, string $message = '')
    {
        $dom = $this->getResponseDom();
        $this->assertEquals($expectedCount, (new \DOMXPath($dom))->query($xpath)->length, $message);
    }

    private function assertDomElementContains(string $xpath, string $expectedString, string $message = '')
    {
        $dom = $this->getResponseDom();
        $this->assertContains($expectedString, $dom->saveHTML((new \DOMXPath($dom))->query($xpath)->item(0)), $message);
    }
    private function getResponseDom(): \DOMDocument
    {
        $dom = new \DOMDocument();
        \libxml_use_internal_errors(true);
        $dom->loadHTML($this->getResponse()->getBody());
        \libxml_use_internal_errors(false);
        return $dom;
    }

}