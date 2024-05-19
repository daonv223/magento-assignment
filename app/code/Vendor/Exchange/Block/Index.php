<?php
declare(strict_types=1);

namespace Vendor\Exchange\Block;

use Magento\Framework\View\Element\Template;
use GuzzleHttp\ClientFactory;

class Index extends Template
{
    public function __construct(
        private ClientFactory $clientFactory,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getExchangeRateList(): array
    {
        $result = [];
        $client = $this->clientFactory->create();
        $response = $client->request('GET', 'https://portal.vietcombank.com.vn/Usercontrols/TVPortal.TyGia/pXML.aspx');
        $xml = simplexml_load_string($response->getBody()->getContents(), 'SimpleXMLElement', LIBXML_NOCDATA);
        $json = json_encode($xml);
        $data = json_decode($json,TRUE);
        $result['DateTime'] = $data['DateTime'];
        $result['Exrates'] = [];
        foreach ($data['Exrate'] as $item) {
            $result['Exrates'][] = [
                'CurrencyCode' => $item['@attributes']['CurrencyCode'],
                'CurrencyName' => $item['@attributes']['CurrencyName'],
                'Buy' => $item['@attributes']['Buy'],
                'Transfer' => $item['@attributes']['Transfer'],
                'Sell' => $item['@attributes']['Sell']
            ];
        }
        return $result;
    }
}
