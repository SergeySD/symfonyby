<?php

namespace Sfby\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SfbyUserBundle extends Bundle
{
    public function getParent()
    {
        return 'SonataUserBundle';
    }

}
