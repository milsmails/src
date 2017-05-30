<?php


namespace Fot\Component\ElvisConnector\Model;

use Akeneo\Component\Classification\CategoryAwareInterface;
use Akeneo\Component\Classification\TagAwareInterface;
use Akeneo\Component\FileStorage\Model\FileInfoInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Pim\Component\Catalog\Model\ChannelInterface;
use Pim\Component\Catalog\Model\LocaleInterface;
use Pim\Component\ReferenceData\Model\ReferenceDataInterface;

/**
 * Product asset interface
 *
 * @author Julien Janvier <jjanvier@akeneo.com>
 */
interface ElvisCollectionInterface extends ReferenceDataInterface, TagAwareInterface, CategoryAwareInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getAssets();

    /**
     * @return LocaleInterface[]|ArrayCollection of LocaleInterface
     */
    public function getLocales();

    /**
     * @return bool
     */
    public function isLocalizable();

    /**
     * @param ArrayCollection of ReferenceInterface $references
     *
     * @return AssetInterface
     */
    public function isEnabled();

    /**
     * @param bool $isEnabled
     */
    public function setEnabled($isEnabled);

    /**
     * @return \DateTime
     */
    public function getEndOfUseAt();

    /**
     * @param \DateTime|null $endOfUseAt
     *
     * @return AssetInterface
     */
    public function setEndOfUseAt($endOfUseAt);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $createdAt
     *
     * @return AssetInterface
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param \DateTime $updatedAt
     *
     * @return AssetInterface
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * Look for the variation corresponding to the specified channel and locale and return its file info.
     *
     * @param ChannelInterface     $channel
     * @param LocaleInterface|null $locale
     *
     * @return FileInfoInterface|null
     */
    public function getAssetsForContext(ChannelInterface $channel, LocaleInterface $locale = null);

    public function setAssets();
}
