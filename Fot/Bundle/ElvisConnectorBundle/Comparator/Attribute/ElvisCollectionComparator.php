<?php

namespace Fot\Bundle\ElvisConnectorBundle\Comparator\Attribute;

use Pim\Component\Catalog\Comparator\ComparatorInterface;


class ElvisCollectionComparator implements ComparatorInterface
{
    /** @var array */
    protected $types;

    /**
     * @param array $types
     */
    public function __construct(array $types)
    {
        $this->types = $types;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($type)
    {
        return in_array($type, $this->types);
    }

    /**
     * {@inheritdoc}
     */
    public function compare($data, $originals)
    {
        $default = ['locale' => null, 'scope' => null, 'data' => null];
        $originals = array_merge($default, $originals);

        if (null === $data['data'] && null === $originals['data']) {
            return null;
        }


        if (null !== $data['data'] && null === $originals['data']) {
            return $data;
        }

        return $data['data'] !==  $originals['data'] ? $data : null;
    }
}
