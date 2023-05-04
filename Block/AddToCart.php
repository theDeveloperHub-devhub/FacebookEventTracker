<?php

declare(strict_types = 1);

namespace DeveloperHub\FacebookEventTracker\Block;

use DeveloperHub\FacebookEventTracker\Model\SessionFactory;
use Magento\Customer\CustomerData\SectionSourceInterface;

class AddToCart implements SectionSourceInterface
{
    /**
     * @var SessionFactory
     */
    private $fbEventTrackerSession;

    /**
     * AddToCart constructor.
     * @param SessionFactory $fbEventTrackerSession
     */
    public function __construct(
        SessionFactory $fbEventTrackerSession
    ) {
        $this->fbEventTrackerSession = $fbEventTrackerSession;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getSectionData()
    {
        $data = [
            'events' => []
        ];

        if ($this->fbEventTrackerSession->create()->hasAddToCart()) {
            $data['events'][] = [
                'eventName' => 'AddToCart',
                'eventAdditional' => $this->fbEventTrackerSession->create()->getAddToCart()
            ];
        }
        return $data;
    }
}
