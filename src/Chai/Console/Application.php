<?php

namespace Chai\Console;

use Symfony\Component\Console\Application as Console;

class Application extends Console
{

    public function register($component)
    {
        $this->addCommands($component->getCommands());
    }

}
