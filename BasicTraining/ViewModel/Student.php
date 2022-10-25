<?php
namespace Cowell\BasicTraining\ViewModel;


use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;

class Student extends AbstractBlock implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    protected $studentFactory;
    protected $request;
    protected $layout;

    public function __construct(
        Template\Context $context,
        \Cowell\BasicTraining\Model\StudentFactory $studentFactory,
        \Magento\Framework\App\RequestInterface $request,
        LayoutInterface $layout,
        array $data = []
    ) {
        $this->studentFactory = $studentFactory;
        $this->request = $request;
        $this->layout = $layout;
        parent::__construct($context, $data);
    }

    public function getAllStudent()
    {
        $get_id = $this->request->getParam('id') ? $this->request->getParam('id') : 'ASC';
        $get_name = $this->request->getParam('key');
        $get_gender = $this->request->getParam('gender');
        $page=($this->request->getParam('p')) ? $this->request->getParam('p') : 1;
        $pageSize=($this->request->getParam('limit')) ? $this->request->getParam('limit') : 5;
        $collection = $this->studentFactory->create()->getCollection()->addOrder('id', $get_id);
        if ($get_name != '') {
            $collection->addFieldToFilter('name', ['like' => '%' . $get_name . '%']);
//            $sql = $collection->__toString();
        }
        if ($get_gender != '') {
            $collection->addFieldToFilter('gender', $get_gender);
        }

        if ($this->request->getParam('start') != '' && $this->request->getParam('end') != '') {
            $collection->addFieldToFilter('dob', ['from'=>$this->request->getParam('start'), 'to'=>$this->request->getParam('end')]);
//            $collection->getSelect()->__toString();
        }

        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        return $collection;
    }

    /**
     * @throws LocalizedException
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getAllStudent()) {
            $pager = $this->layout->createBlock(
                'Magento\Theme\Block\Html\Pager'
            )->setAvailableLimit([5=>5,10=>10,15=>15,20=>20])->setShowPerPage(false)->setCollection($this->getAllStudent());
            $this->setChild('pager', $pager);
            $this->getAllStudent()->load();
        }
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
