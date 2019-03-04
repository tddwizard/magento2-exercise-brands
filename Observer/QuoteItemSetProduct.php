<?php
declare(strict_types=1);

namespace TddWizard\ExerciseBrands\Observer;


use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;

class QuoteItemSetProduct implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        /** @var ProductInterface $product */
        $product = $observer->getData('product');
        /** @var Quote\Item $quoteItem */
        $quoteItem = $observer->getData('quote_item');
        $brandAttribute = $product->getCustomAttribute('brand');
        if ($brandAttribute) {
            $quoteItem->setData('brand', $brandAttribute->getValue());
        }
    }
}
