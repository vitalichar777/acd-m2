<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Thecommerceshop\Map\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Thecommerceshop\Map\Api\Data\DiscountInterface;
use Thecommerceshop\Map\Api\Data\DiscountInterfaceFactory;
use Thecommerceshop\Map\Api\Data\DiscountSearchResultsInterfaceFactory;
use Thecommerceshop\Map\Api\DiscountRepositoryInterface;
use Thecommerceshop\Map\Model\ResourceModel\Discount as ResourceDiscount;
use Thecommerceshop\Map\Model\ResourceModel\Discount\CollectionFactory as DiscountCollectionFactory;

class DiscountRepository implements DiscountRepositoryInterface
{

    /**
     * @var ResourceDiscount
     */
    protected $resource;

    /**
     * @var DiscountInterfaceFactory
     */
    protected $discountFactory;

    /**
     * @var DiscountCollectionFactory
     */
    protected $discountCollectionFactory;

    /**
     * @var Discount
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;


    /**
     * @param ResourceDiscount $resource
     * @param DiscountInterfaceFactory $discountFactory
     * @param DiscountCollectionFactory $discountCollectionFactory
     * @param DiscountSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceDiscount $resource,
        DiscountInterfaceFactory $discountFactory,
        DiscountCollectionFactory $discountCollectionFactory,
        DiscountSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->discountFactory = $discountFactory;
        $this->discountCollectionFactory = $discountCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(DiscountInterface $discount)
    {
        try {
            $this->resource->save($discount);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the discount: %1',
                $exception->getMessage()
            ));
        }
        return $discount;
    }

    /**
     * @inheritDoc
     */
    public function get($productId)
    {
        $discount = $this->discountFactory->create();
        $this->resource->load($discount, $productId);
        if (!$discount->getId()) {
            throw new NoSuchEntityException(__('Discount with id "%1" does not exist.', $productId));
        }
        return $discount;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->discountCollectionFactory->create();
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(DiscountInterface $discount)
    {
        try {
            $discountModel = $this->discountFactory->create();
            $this->resource->load($discountModel, $discount->getProductId());
            $this->resource->delete($discountModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Discount: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($productId)
    {
        return $this->delete($this->get($productId));
    }
}

