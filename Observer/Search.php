<?php

declare(strict_types = 1);

namespace DeveloperHub\FacebookEventTracker\Observer;

use DeveloperHub\FacebookEventTracker\Helper\Data;
use DeveloperHub\FacebookEventTracker\Model\SessionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Search implements ObserverInterface
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
     * @var \Magento\Search\Helper\Data
     */
    private $searchHelper;
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * Search constructor.
     * @param Data $helper
     * @param \Magento\Search\Helper\Data $searchHelper
     * @param RequestInterface $request
     * @param SessionFactory $fbEventTrackerSession
     */
    public function __construct(
        Data $helper,
        \Magento\Search\Helper\Data $searchHelper,
        RequestInterface $request,
        SessionFactory $fbEventTrackerSession
    ) {
        $this->fbEventTrackerSession = $fbEventTrackerSession;
        $this->fbEventTrackerHelper         = $helper;
        $this->searchHelper = $searchHelper;
        $this->request = $request;
    }

    /**
     * @param Observer $observer
     *
     * @return boolean
     */
    public function execute(Observer $observer)
    {
        $text = $this->searchHelper->getEscapedQueryText();
        if (!$text) {
            $text = $this->request->getParams();
            foreach ($this->request->getParams() as $key => $value) {
                $text[$key] = $value;
            }
        }
        if (!$this->fbEventTrackerHelper->isSearch() || !$text) {
            return true;
        }

        $data = [
            'search_string' => $text
        ];
        $this->fbEventTrackerSession->create()->setSearch($data);

        return true;
    }
}
