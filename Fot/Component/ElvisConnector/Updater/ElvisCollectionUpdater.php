<?php

namespace Fot\Component\ElvisConnector\Updater;

use Akeneo\Component\Classification\Repository\CategoryRepositoryInterface;
use Akeneo\Component\Classification\Repository\TagRepositoryInterface;
use Akeneo\Component\StorageUtils\InvalidObjectException;
use Akeneo\Component\StorageUtils\InvalidPropertyException;
use Akeneo\Component\StorageUtils\Updater\ObjectUpdaterInterface;
use Doctrine\Common\Util\ClassUtils;
use Fot\Component\ElvisConnector\Factory\ElvisCollectionFactory;
use Fot\Component\ElvisConnector\Model\ElvisCollectionInterface;
use Pim\Component\Catalog\Updater\Setter\SetterInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Updates and validates a asset
 *
 * @author Olivier Soulet <olivier.soulet@akeneo.com>
 */
class ElvisCollectionUpdater implements ObjectUpdaterInterface , SetterInterface
{
    /** @var AssetFactory */
    protected $assetFactory;

    /** @var PropertyAccessor */
    protected $accessor;

    /**
     * @param TagRepositoryInterface      $tagRepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param AssetFactory                $assetFactory
     */
    public function __construct(
        ElvisCollectionFactory $assetFactory
    ) {
        $this->assetFactory = $assetFactory;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function update($asset, array $data, array $options = [])
    {
        if (!$asset instanceof ElvisCollectionInterface) {
            throw InvalidObjectException::objectExpected(
                ClassUtils::getClass($asset),
                ElvisCollectionInterface::class
            );
        }

        foreach ($data as $field => $item) {
            $this->setData($asset, $field, $item);
        }

        return $this;
    }

    /**
     * @param AssetInterface $asset
     * @param string         $field
     * @param mixed          $data
     *
     * @throws InvalidPropertyException
     */
    protected function setData(ElvisCollectionInterface $asset, $field, $data)
    {
      /*  switch ($field) {
            case 'tags':
                $this->setTags($asset, $data);
                break;
            case 'categories':
                $this->setCategories($asset, $data);
                break;
            case 'end_of_use':
                $this->validateDateFormat($data);
                $asset->setEndOfUseAt(new \DateTime($data));
                break;
            case 'localized':
                $this->setLocalized($asset, $data);
                break;
            default:*/
                $this->accessor->setValue($asset, $field, $data);
        //}
    }

    /**
     * @param AssetInterface $asset
     * @param bool           $isLocalized
     */
    protected function setLocalized(ElvisCollectionInterface $asset, $isLocalized)
    {
        $this->assetFactory->createReferences($asset, $isLocalized);
    }
}
