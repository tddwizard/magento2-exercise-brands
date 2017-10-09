<?php

namespace TddWizard\ExerciseBrands\Test\Integration;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{
    public function testBrandAttributeExists()
    {
        /** @var ProductAttributeRepositoryInterface $attributeRepository */
        $attributeRepository = Bootstrap::getObjectManager()->get(ProductAttributeRepositoryInterface::class);
        $this->assertInstanceOf(
            \Magento\Catalog\Api\Data\ProductAttributeInterface::class,
            $attributeRepository->get('brand')
        );
    }
}