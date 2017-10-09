<?php

namespace TddWizard\ExerciseBrands\Api;

use Magento\Catalog\Api\Data\ProductInterface;

interface ProductBrandRepositoryInterface
{
    /**
     * @param string $brand
     * @return ProductInterface[]
     */
    public function getProductsByBrand($brand);
}