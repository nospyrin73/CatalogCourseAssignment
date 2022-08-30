<?php

declare(strict_types = 1);

namespace Scandiweb\Test\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\App\State;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;

class CreateSimpleProduct implements DataPatchInterface
{
    protected State $state;

    protected ProductInterfaceFactory $productFactory;

    protected ProductRepositoryInterface $productRepository;

    protected EavSetup $eavSetup;

    protected SourceItemInterfaceFactory $sourceItemFactory;

    protected SourceItemsSaveInterface $sourceItemsSave;

    protected array $sourceItems;

    public function __construct(
        State $state,
        ProductInterfaceFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        EavSetup $eavSetup,
        SourceItemInterfaceFactory $sourceItemFactory,
        SourceItemsSaveInterface $sourceItemsSave,
        CategoryLinkManagementInterface $categoryLink
    )
    {
        $this->state = $state;
        $this->productInterfaceFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->eavSetup = $eavSetup;
        $this->sourceItemFactory = $sourceItemFactory;
        $this->sourceItemsSave = $sourceItemsSave;
        $this->sourceItems = [];
    }

    public function apply()
    {
        $this->state->emulateAreaCode('execute', [$this, 'execute']);
    }

    public function execute()
    {
        // Create product
        $product = $this->productFactory->create();

        if ($product->getIdBySku('rtx-3080')) {
            return;
        }

        $attributeSetId = $this->eavSetup->getAttributeSetId(Product::ENTITY, 'Default');

        // Set default product attributes
        $product
            ->setTypeId(Type::TYPE_SIMPLE)
            ->setSku('rtx-3080')
            ->setName('Nvidia RTX 3080')
            ->setUrlKey('rtx3080')
            ->setPrice(699.00)
            ->setVisibility(Visibility::VISIBILITY_BOTH)
            ->setStatus(Status::STATUS_ENABLED)
            ->setAttributeSetId($attributeSetId);

        // Save the product
        $product = $this->productRepository->save($product);

        // Add inventory to the product
        $sourceItem = $this->sourceItemFactory->create();
        $sourceItem->setSourceCode('default');
        $sourceItem->setQuantity(20);
        $sourceItem->setSku($product->getSku());
        $sourceItem->setStatus(SourceItemInterface::STATUS_IN_STOCK);

        $this->sourceItems[] = $sourceItem;

        $this->sourceItemsSave->execute($this->sourceItems);
    }

    public function getAliases(): array {
        return [];
    }

    public static function getDependencies(): array {
        return [];
    }
}
