<?php

namespace NewEcomAI\ShopSmart\Model\Config\Source;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;

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

    /**
     * CmsBlocks constructor.
     * @param BlockRepositoryInterface $blockRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        BlockRepositoryInterface $blockRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->blockRepository = $blockRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     * @throws LocalizedException
     */
    public function toOptionArray(): array
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $cmsBlocks = $this->blockRepository->getList($searchCriteria)->getItems();

        $arrResult = [];

        foreach ($cmsBlocks as $block) {
            $arrResult[] = ['value' => $block->getIdentifier(), 'label' => $block->getTitle()];
        }
        return $arrResult;
    }
}
