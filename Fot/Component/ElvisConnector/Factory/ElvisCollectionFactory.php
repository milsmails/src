<?php


namespace Fot\Component\ElvisConnector\Factory;

use Akeneo\Component\StorageUtils\Factory\SimpleFactoryInterface;
use Pim\Component\Catalog\Repository\LocaleRepositoryInterface;
use Fot\Component\ElvisConnector\Model\ElvisCollectionInterface;

/**
 * Asset factory
 *
 * @author Willy Mesnage <willy.mesnage@akeneo.com>
 */
class ElvisCollectionFactory implements SimpleFactoryInterface
{

    /** @var LocaleRepositoryInterface */
    protected $localeRepository;

    /** @var string */
    protected $assetClass;

    /**
     * @param ReferenceFactory          $referenceFactory
     * @param LocaleRepositoryInterface $localeRepository
     * @param string                    $assetClass
     */
    public function __construct(
        LocaleRepositoryInterface $localeRepository,
        $assetClass
    ) {
        $this->localeRepository = $localeRepository;
        $this->assetClass = $assetClass;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return new $this->assetClass();
    }

}
