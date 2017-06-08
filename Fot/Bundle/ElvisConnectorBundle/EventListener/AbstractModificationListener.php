<?php
/**
 * AbstractModificationListener.php
 *
 * @author    Matthias Mahler <m.mahler@eikona.de>
 * @copyright 2017 Eikona AG (http://www.eikona.de)
 */

    namespace Eikona\Tessa\ConnectorBundle\EventListener;

    use Eikona\Tessa\ConnectorBundle\Tessa;

abstract class AbstractModificationListener
{
    /**
     * @var Tessa
     */
    protected $tessa;

    /**
     * AbstractEntityModificationListener constructor.
     *
     * @param $tessa
     */
    public function __construct(Tessa $tessa)
    {
        $this->tessa = $tessa;
    }

}