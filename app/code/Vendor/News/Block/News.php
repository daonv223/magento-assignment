<?php
declare(strict_types=1);

namespace Vendor\News\Block;

use Magento\Framework\View\Element\Template;

class News extends Template
{
    public function getFetchUrl(): string
    {
        return $this->getUrl('news/ajax/load');
    }
}
