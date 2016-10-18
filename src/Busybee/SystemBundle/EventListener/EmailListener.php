<?php
namespace Busybee\SystemBundle\EventListener ;


class EmailListener implements \Swift_Events_SendListener
{

    public $beforeSendEvt = null;
    public $sendEvt = null;

    public function beforeSendPerformed(\Swift_Events_SendEvent $evt)
    {
        $this->beforeSendEvt = $evt;
    }

    public function sendPerformed(\Swift_Events_SendEvent $evt)
    {
        $this->sendEvt = $evt;
    }
}
