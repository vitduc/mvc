<?php

namespace Nsk\PartNumbersInterchange\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class Competitor implements ArrayInterface
{
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function toOptionArray()
    {
        $competitors = $this->scopeConfig->getValue('configuration/competitors/lists', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $result = [];

        foreach ($competitors as $index => $value) {
            $result[] = ['value' => $value['value'], 'label' => $value['label']];
        }
        return $result;
    }
}
