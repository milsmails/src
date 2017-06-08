<?php
    /**
     * ProductModificationListener.php
     *
     * @author    Matthias Mahler <m.mahler@eikona.de>
     * @copyright 2017 Eikona AG (http://www.eikona.de)
     */

namespace Fot\Bundle\ElvisConnectorBundle\EventListener;

use Pim\Component\Catalog\Model\ProductInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class ProductModificationListener extends AbstractModificationListener
{

    public function onPostSave(GenericEvent $event)
    {
        $subject = $event->getSubject();

        if ($subject instanceof ProductInterface) {
            $this->tessa->notifyAboutProductModifications($subject);
        }


    }
}