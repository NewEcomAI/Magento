<?php

namespace NewEcomAI\ShopSmart\Model\Config\Source;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\FilterBuilder;

class CmsBlocks implements OptionSourceInterface
{
    /**
     * @var BlockRepositoryInterface
     */
    protected BlockRepositoryInterface $blockRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;
    private FilterBuilder $filterBuilder;

    /**
     * CmsBlocks constructor.
     * @param BlockRepositoryInterface $blockRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        BlockRepositoryInterface $blockRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder           $filterBuilder
    ) {
        $this->blockRepository = $blockRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     * @throws LocalizedException
     */
    public function toOptionArray(): array
    {
        $filters = [];
        $blocks = PopupLayout::$popupLayout;
        foreach ($blocks as $block) {
            $filters[] = $this->filterBuilder
                ->setField('identifier')
                ->setConditionType('eq')
                ->setValue($block)
                ->create();

        }
        $this->searchCriteriaBuilder->addFilters($filters);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $cmsBlocks = $this->blockRepository->getList($searchCriteria)->getItems();
        $arrResult = [];
        foreach ($cmsBlocks as $block) {
            $arrResult[] = ['value' => $block->getIdentifier(), 'label' => $block->getTitle()];
        }
        return $arrResult;
    }
}
