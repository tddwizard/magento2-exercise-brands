<?php
declare(strict_types=1);

namespace TddWizard\ExerciseBrands\Plugin;

use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Model\Quote;

class QuoteItemRepositoryPlugin
{
    public function beforeSave(CartItemRepositoryInterface $subject, Quote\Item $item)
    {
        $item->setData('brand', $item->getExtensionAttributes()->getBrand() ?? $item->getData('brand'));
        return [$item];
    }

    public function afterGetList(CartItemRepositoryInterface $subject, array $result)
    {
        foreach ($result as $item) {
            $item->getExtensionAttributes()->setBrand($item->getData('brand'));
        }
        return $result;
    }
}