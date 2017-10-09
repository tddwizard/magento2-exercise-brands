<?php

namespace TddWizard\ExerciseBrands\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use TddWizard\ExerciseBrands\Api\ProductBrandRepositoryInterface;

class ProductBrandRepository implements ProductBrandRepositoryInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder)
    {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param string $brand
     * @return ProductInterface[]
     */
    public function getProductsByBrand($brand)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('brand', $brand)->create();
        return $this->productRepository->getList($searchCriteria)->getItems();
    }

}