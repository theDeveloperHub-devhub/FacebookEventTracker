<?php

declare(strict_types = 1);

namespace DeveloperHub\FacebookEventTracker\Block;

use DeveloperHub\FacebookEventTracker\Model\SessionFactory;
use Magento\Customer\CustomerData\SectionSourceInterface;

class Subscribe implements SectionSourceInterface
{
    /**
     * @var SessionFactory
     */
    private $fbEventTrackerSession;

    /**
     * Subscribe constructor.
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

        if ($this->fbEventTrackerSession->create()->hasAddSubscribe()) {
            $data['events'][] = [
                'eventName' => 'Subscribe',
                'eventAdditional' => $this->fbEventTrackerSession->create()->getAddSubscribe()
            ];
        }
        return $data;
    }
}
