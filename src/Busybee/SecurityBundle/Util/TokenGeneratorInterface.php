<?php
namespace Busybee\SecurityBundle\Util;

interface TokenGeneratorInterface
{
    /**
     * @return string
     */
    public function generateToken();
}
