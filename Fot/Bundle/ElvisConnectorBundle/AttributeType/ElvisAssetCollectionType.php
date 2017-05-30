<?php

namespace Fot\Bundle\ElvisConnectorBundle\AttributeType;

use Pim\Bundle\CatalogBundle\AttributeType\AbstractAttributeType;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\ProductValueInterface;
use Pim\Component\Catalog\Validator\ConstraintGuesserInterface;
use Pim\Component\ReferenceData\ConfigurationRegistryInterface;

class ElvisAssetCollectionType extends AbstractAttributeType
{
    protected $referenceRegistry;
    public function __construct(
        $backendType,
        $formType,
        ConstraintGuesserInterface $constraintGuesser,
        ConfigurationRegistryInterface $registry
    ) {
        parent::__construct($backendType, $formType, $constraintGuesser);

        $this->referenceRegistry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareValueFormName(ProductValueInterface $value)
    {
        $referenceDataConf = $this->referenceRegistry->get($value->getAttribute()->getReferenceDataName());

        return $referenceDataConf->getName();
    }

    /**
     * {@inheritdoc}
     */
    protected function defineCustomAttributeProperties(AttributeInterface $attribute)
    {
        $attributes = parent::defineCustomAttributeProperties($attribute);

        unset(
            $attributes['availableLocales'],
            $attributes['unique'],
            $attributes['localizable'],
            $attributes['scopable']
        );

        return $attributes + [
                'reference_data_name' => [
                    'name'      => 'reference_data_name',
                    'fieldType' => 'hidden',
                    'options'   => [
                        'data' => 'elvis-assets',
                    ],
                ],
                'scopable' => [
                    'name'      => 'scopable',
                    'fieldType' => 'switch',
                    'options'   => [
                        'data'      => true,
                        'disabled'  => false,
                        'read_only' => false
                    ]
                ],
                'localizable' => [
                    'name'      => 'localizable',
                    'fieldType' => 'switch',
                    'options'   => [
                        'data'      => false,
                        'disabled'  => true,
                        'read_only' => true
                    ]
                ],
            ];
    }

    public function getName()
    {
        return AttributeTypes::ELVIS_ASSETS_COLLECTION;
    }
}
