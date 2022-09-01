<?php

declare(strict_types = 1);

namespace Scandiweb\Test\Setup\Patch\Data;

use Magento\Catalog\Setup\CategorySetup;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class CreateCategory implements DataPatchInterface
{
    /**
     * @var CategorySetup
     */
    protected CategorySetup $categorySetup;

    /**
     * @var CategoryCollectionFactory
     */
    protected CategoryCollectionFactory $categoryCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @param CategorySetup $categorySetup
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CategorySetup $categorySetup,
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface     $storeManager
    ) {
        $this->categorySetup = $categorySetup;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @return void
     */
    public function apply(): void
    {
        $parentId = $this->storeManager->getStore()->getRootCategoryId();

        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToFilter('url_key', ['eq' => 'gpus']);
        $newCategory = $collection->getFirstItem();

        if ($parentId && !$newCategory->getId()) {
            $newCategory = $this->categorySetup->createCategory(
                [
                    'data' => [
                        'parent_id' => $parentId,
                        'name' => 'GPUs',
                        'is_active' => true,
                        'include_in_menu' => true,
                    ],
                ]
            );
            $newCategory->save();
        }
    }

    /**
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }
}