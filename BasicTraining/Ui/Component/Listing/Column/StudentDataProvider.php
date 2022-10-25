<?php

namespace Cowell\BasicTraining\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class StudentDataProvider extends Column
{

    /**
     * URL builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    public $_urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface   $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface       $urlBuilder,
        array              $components = [],
        array              $data = []
    ) {
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if ($item['gender']==1) {
                    $item['gender'] = 'Male';
                } else {
                    $item['gender'] = 'Female';
                }
            }
        }
        return $dataSource;
    }
}
