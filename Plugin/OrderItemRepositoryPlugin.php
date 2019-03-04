<?php
declare(strict_types=1);

namespace TddWizard\ExerciseBrands\Plugin;

use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\Data\OrderItemSearchResultInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Model\Order;

class OrderItemRepositoryPlugin
{
    public function beforeSave(OrderItemRepositoryInterface $subject, Order\Item $item)
    {
        $item->setData('brand', $item->getExtensionAttributes()->getBrand() ?? $item->getData('brand'));
        return [$item];
    }

    public function afterGet(OrderItemRepositoryInterface $subject, Order\Item $item)
    {
        $item->getExtensionAttributes()->setBrand($item->getData('brand'));
        return $item;
    }
    
    public function afterGetList(OrderItemRepositoryInterface $subject, OrderItemSearchResultInterface $result)
    {
        foreach ($result->getItems() as $item) {
            $item->getExtensionAttributes()->setBrand($item->getData('brand'));
        }
        return $result;
    }
}