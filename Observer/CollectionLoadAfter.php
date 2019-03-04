<?php
declare(strict_types=1);

namespace TddWizard\ExerciseBrands\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\ResourceModel\Quote;

/*
 * Not QuoteItemRepository is used to load quote items for quote, but plain collection. And it does not even have an
 * event prefix
 */
class CollectionLoadAfter implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $collection = $observer->getData('collection');
        if ($collection instanceof Quote\Item\Collection) {
            foreach ($collection as $item) {
                $item->getExtensionAttributes()->setBrand($item->getData('brand'));
            }
        }
    }

}