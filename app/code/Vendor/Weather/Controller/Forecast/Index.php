<?php

namespace Vendor\Weather\Controller\Forecast;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

class Index implements HttpGetActionInterface
{
    public function __construct(
        private PageFactory $pageFactory
    )
    {
    }

    public function execute()
    {
        return $this->pageFactory->create();
    }
}
