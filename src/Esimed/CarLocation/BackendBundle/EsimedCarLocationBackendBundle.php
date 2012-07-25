<?php

namespace Esimed\CarLocation\BackendBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class EsimedCarLocationBackendBundle extends Bundle {
    public function boot()
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $evm = $em->getEventManager();
        // Timestampable
        $evm->addEventSubscriber(new \Gedmo\Timestampable\TimestampableListener());
    }
}
