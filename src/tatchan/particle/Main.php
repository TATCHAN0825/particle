<?php

declare(strict_types=1);

namespace tatchan\particle;

use pocketmine\plugin\PluginBase;
use tatchan\particle\Commands\ParticleCommand;

class Main extends PluginBase
{
    public function onEnable() {
        $this->getServer()->getCommandMap()->register($this->getName(), new ParticleCommand($this));
    }
}
