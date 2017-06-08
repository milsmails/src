<?php

namespace Fot\Component\ElvisConnector\Updater\Setter;
use Pim\Component\Catalog\Builder\ProductBuilderInterface;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Updater\Setter\AbstractAttributeSetter;
use Pim\Component\Catalog\Updater\Setter\TextAttributeSetter;
use Pim\Component\Catalog\Validator\AttributeValidatorHelper;

class ElvisCollectionSetter extends  TextAttributeSetter {

    public function __construct(
        ProductBuilderInterface $productBuilder,
        AttributeValidatorHelper $attrValidatorHelper,
        array $supportedTypes
    ) {
        parent::__construct($productBuilder,
        $attrValidatorHelper,
         $supportedTypes);
        $this->supportedTypes = $supportedTypes;
    }

    public function setAttributeData(
        ProductInterface $product,
        AttributeInterface $attribute,
        $data,
        array $options = []
    )
    {
        $options = $this->resolver->resolve($options);
        $this->checkLocaleAndScope($attribute, $options['locale'], $options['scope']);
        $this->checkData($attribute, $data);

        $this->setData($product, $attribute, $data, $options['locale'], $options['scope']);
    }

public function checkData(AttributeInterface $attribute,$data) {return true;}


}