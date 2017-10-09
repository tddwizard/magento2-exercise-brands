<?php

namespace TddWizard\ExerciseBrands\Test\Integration;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use TddWizard\ExerciseBrands\Api\ProductBrandRepositoryInterface;
use TddWizard\Fixtures\Catalog\ProductBuilder;
use TddWizard\Fixtures\Catalog\ProductFixture;
use TddWizard\Fixtures\Catalog\ProductFixtureRollback;

/**
 * @magentoDbIsolation enabled
 */
class ProductBrandRepositoryTest extends TestCase
{
    /**
     * @var ProductFixture[]
     */
    private $productFixtures;

    protected function setUp()
    {
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

    public function testGetProductsByBrand()
    {
        /** @var ProductBrandRepositoryInterface $repository */
        $repository = Bootstrap::getObjectManager()->get(ProductBrandRepositoryInterface::class);
        $result = $repository->getProductsByBrand('nike');
        $this->assertCount(4, $result);
        $this->assertEquals(
            ['Nike Air', 'Nike Earth', 'Nike Fire', 'Nike Water'],
            array_values(
                array_map(
                    function (ProductInterface $product) {
                        return $product->getName();
                    },
                    $result
                )
            )
        );
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
}