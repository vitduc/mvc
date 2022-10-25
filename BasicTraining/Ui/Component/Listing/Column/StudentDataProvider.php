<?php

namespace Cowell\BasicTraining\Ui\Component\Listing\Column;


class StudentDataProvider extends \Magento\Ui\Component\Listing\Columns\Column
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
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
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

//                if (isset($item['id'])) {
//                    $item[$this->getData('name')] = [
//                        'edit' => [
//                            'href' => $this->urlBuilder->getUrl(
//                                static::URL_PATH_EDIT,
//                                [
//                                    'training_id' => $item['training_id']
//                                ]
//                            ),
//                            'label' => __('Edit')
//                        ],
//                        'delete' => [
//                            'href' => $this->urlBuilder->getUrl(
//                                static::URL_PATH_DELETE,
//                                [
//                                    'training_id' => $item['training_id']
//                                ]
//                            ),
//                            'label' => __('Delete'),
//                            'confirm' => [
//                                'title' => __('Delete "${ $.$data.title }"'),
//                                'message' => __('Are you sure you wan\'t to delete a "${ $.$data.title }" record?')
//                            ]
//                        ]
//                    ];
//                }
            }
        }
        return $dataSource;
    }
}
