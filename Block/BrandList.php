<?php

namespace TddWizard\ExerciseBrands\Block;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use TddWizard\ExerciseBrands\Api\ProductBrandRepositoryInterface;

class BrandList extends Template
{
    /**
     * Core registry
     *
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var ProductBrandRepositoryInterface
     */
    private $productBrandRepository;

    public function __construct(
        Template\Context $context,
        Registry $coreRegistry,
        ProductBrandRepositoryInterface $productBrandRepository,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->coreRegistry = $coreRegistry;
        $this->productBrandRepository = $productBrandRepository;
    }

    public function getBrand()
    {
        return $this->getProduct()->getData('brand');
    }

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getProducts()
    {
        return array_diff_key(
            $this->productBrandRepository->getProductsByBrand($this->getBrand()),
            [$this->getProduct()->getId() => null]
        );
    }

    /**
     * Retrieve currently viewed product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', $this->coreRegistry->registry('product'));
        }
        return $this->getData('product');
    }
}