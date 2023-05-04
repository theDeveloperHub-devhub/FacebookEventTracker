<?php

declare(strict_types = 1);

namespace DeveloperHub\FacebookEventTracker\Observer;

use DeveloperHub\FacebookEventTracker\Helper\Data;
use DeveloperHub\FacebookEventTracker\Model\SessionFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Register implements ObserverInterface
{

    /**
     * @var SessionFactory
     */
    private $fbEventTrackerSession;

    /**
     * @var Data
     */
    private $fbEventTrackerHelper;

    /**
     * Register constructor.
     * @param SessionFactory $fbEventTrackerSession
     * @param Data $helper
     */
    public function __construct(
        SessionFactory $fbEventTrackerSession,
        Data $helper
    ) {
        $this->fbEventTrackerSession = $fbEventTrackerSession;
        $this->fbEventTrackerHelper  = $helper;
    }

    /**
     * @param Observer $observer
     * @return boolean
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        if (!$this->fbEventTrackerHelper->isRegistration()
            || !$customer
        ) {
            return true;
        }
        $data = [
            'customer_id' => $customer->getId(),
            'email' => $customer->getEmail(),
            'fn' => $customer->getFirstName(),
            'ln' => $customer->getLastName()
        ];

        $this->fbEventTrackerSession->create()->setRegister($data);

        return true;
    }
}
