<?php

namespace Busybee\HomeBundle\Model;

class FlashExtension extends \Twig_Extension
{
    /**
     * @var array
     */
    private $flashMessage = [];

    /**
     * @return string
     */
    public function getName()
    {
        return 'flash_twig_extension';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('showFlash', array($this, 'intVal')),
        );
    }

    /**
     * @param   string $value
     * @return  bool
     */
    public function showFlash($value): bool
    {
        if (in_array($value, $this->flashMessage))
            return false;

        $this->flashMessage[] = $value;
        return true;
    }
}