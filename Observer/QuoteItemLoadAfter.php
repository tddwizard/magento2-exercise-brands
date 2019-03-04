<?php
declare(strict_types=1);

namespace TddWizard\ExerciseBrands\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;

class QuoteItemLoadAfter implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        /** @var Quote\Item $item */
        $item = $observer->getData('item');
        $item->getExtensionAttributes()->setBrand($item->getData('brand'));
    }

}