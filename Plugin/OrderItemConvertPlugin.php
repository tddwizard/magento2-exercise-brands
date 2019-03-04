<?php
declare(strict_types=1);

namespace TddWizard\ExerciseBrands\Plugin;

use Magento\Quote\Model\Quote\Item\ToOrderItem;
use Magento\Sales\Api\Data\OrderItemInterface;
use \Magento\Quote\Model\Quote;

class OrderItemConvertPlugin
{
    public function afterConvert(ToOrderItem $subject, OrderItemInterface $result, Quote\Item $source)
    {
        $result->getExtensionAttributes()->setBrand($source->getExtensionAttributes()->getBrand());
        return $result;
    }
}