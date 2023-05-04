<?php

declare(strict_types = 1);

namespace DeveloperHub\FacebookEventTracker\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Pages implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'cms_page', 'label' => 'Cms Page'],
            ['value' => 'account_page', 'label' => 'Account Page'],
            ['value' => 'registration_page', 'label' => 'Registration Page'],
            ['value' => 'checkout_page', 'label' => 'Checkout Page'],
            ['value' => 'success_page', 'label' => 'Success Page'],
            ['value' => 'search_page', 'label' => 'Search Page'],
            ['value' => 'advanced_search_page', 'label' => 'Advanced Search Page']
        ];
    }
}
