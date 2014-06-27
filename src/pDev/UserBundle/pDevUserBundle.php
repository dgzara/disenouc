<?php

namespace pDev\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class pDevUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
