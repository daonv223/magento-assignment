<?php
declare(strict_types=1);

namespace Vendor\News\Controller\Ajax;

use GuzzleHttp\ClientFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

class Load implements HttpGetActionInterface
{
    public function __construct(
        private JsonFactory $jsonFactory,
        private ClientFactory $clientFactory
    ) {
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $json = $this->jsonFactory->create();
        $json->setHeader('Cache-Control', 'max-age=0, must-revalidate, no-cache, no-store', true);
        $json->setHeader('Pragma', 'no-cache', true);
        return $json->setData($this->fetch());
    }

    private function fetch()
    {
        $client = $this->clientFactory->create();
        try {
            $xmlContents =  $client->get('https://vnexpress.net/rss/kinh-doanh.rss')->getBody()->getContents();
            $xml = simplexml_load_string($xmlContents, 'SimpleXMLElement', LIBXML_NOCDATA);
            $json = json_encode($xml);
            $data = json_decode($json,TRUE);
            unset($data['@attributes']);
            return $data;
        } catch (\Exception $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }
}
