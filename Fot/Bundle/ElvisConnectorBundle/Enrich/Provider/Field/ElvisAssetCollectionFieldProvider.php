<?php


namespace Fot\Bundle\ElvisConnectorBundle\Enrich\Provider\Field;

use Pim\Bundle\EnrichBundle\Provider\Field\FieldProviderInterface;
use Pim\Component\Catalog\Model\AttributeInterface;
use Fot\Bundle\ElvisConnectorBundle\AttributeType\AttributeTypes;
/**
 * Field provider for asset collections
 *
 * @author Julien Sanchez <julien@akeneo.com>
 */
class ElvisAssetCollectionFieldProvider implements FieldProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getField($attribute)
    {
        return 'fot-elvis-asset-collection-field';
    }

    /**
     * {@inheritdoc}
     */
    public function supports($element)
    {
        return $element instanceof AttributeInterface &&
            AttributeTypes::ELVIS_ASSETS_COLLECTION ===  $element->getAttributeType();
    }
}
