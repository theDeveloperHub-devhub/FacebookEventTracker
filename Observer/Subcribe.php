<?php

declare(strict_types = 1);

namespace DeveloperHub\FacebookEventTracker\Observer;

use DeveloperHub\FacebookEventTracker\Helper\Data;
use DeveloperHub\FacebookEventTracker\Model\SessionFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Subcribe implements ObserverInterface
{
    /**
     * @var SessionFactory
     */
    private $fbEventTrackerSession;

    /**
     * @var Data
     */
    private $helper;

    /**
     * Subcribe constructor.
     * @param SessionFactory $fbEventTrackerSession
     * @param Data $helper
     */
    public function __construct(
        SessionFactory $fbEventTrackerSession,
        Data $helper
    ) {
        $this->fbEventTrackerSession = $fbEventTrackerSession;
        $this->helper        = $helper;
    }

    /**
     * @param Observer $observer
     *
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $email = $observer->getEvent()->getSubscriber()->getSubscriberEmail();
        $subscribeId =$observer->getEvent()->getSubscriber()->getSubscriberId();
        if (!$this->helper->isSubscribe() || !$email) {
            return true;
        }

        $data = [
            'id' => $subscribeId,
            'email' => $observer->getEvent()->getSubscriber()->getSubscriberEmail()
        ];

        $this->fbEventTrackerSession->create()->setAddSubscribe($data);

        return true;
    }
}
