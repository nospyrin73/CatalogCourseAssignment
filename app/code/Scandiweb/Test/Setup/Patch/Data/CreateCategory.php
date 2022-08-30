<?php

declare(strict_types = 1);

namespace Scandiweb\Test\Setup\Patch\Data;

use Magento\Catalog\Setup\CategorySetup;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

class CreateCategory implements DataPatchInterface
{
    protected CategorySetup $categorySetup;

    protected CategoryCollectionFactory $categoryCollectionFactory;

    public function __construct(
        CategorySetup $categorySetup,
        CategoryCollectionFactory $categoryCollectionFactory
    ) {
        $this->categorySetup = $categorySetup;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    public function apply()
    {
        $parentId = $this->storeManagerInterface->getStore()->getRootCategoryId();

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

    public function getAliases(): array {
        return [];
    }

    public static function getDependencies(): array {
        return [];
    }
}