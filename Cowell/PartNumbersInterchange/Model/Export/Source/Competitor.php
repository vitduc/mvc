<?php

namespace Nsk\PartNumbersInterchange\Model\Export\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Competitor
 * Retrieve options of Competitor
 *
 * @author    cowell
 * @copyright Co-well All Rights Reserved
 * @category
 * @package Nsk\PartNumbersInterchange\Model\Export\Source
 */
class Competitor extends AbstractSource
{
    const XML_PATH_LIST_COMPETITOR = 'configuration/competitors/lists';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $competitors = $this->scopeConfig->getValue(self::XML_PATH_LIST_COMPETITOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if (!is_array($competitors)) {
            $competitors = json_decode($competitors, true);
        }

        $options = [];
        foreach ($competitors as $competitor) {
            $options[] = [
                'value' => $competitor['value'],
                'label' => $competitor['label']
            ];
        }

        return $options;
    }
}
