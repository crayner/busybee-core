<?php
namespace Busybee\SystemBundle\EventListener ;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent ;

class PDOConnectionListener
{
     public function onPdoException(GetResponseForExceptionEvent $event)
     {
          $exception = $event->getException();

          if ($exception instanceof PDOException) {
             dump($exception);
			 die();
          }
     }
}