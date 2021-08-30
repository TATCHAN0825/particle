<?php

namespace tatchan\particle\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\level\particle\DustParticle;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskHandler;
use pocketmine\utils\TextFormat;


class ParticleCommand extends PluginCommand implements CommandExecutor
{
    /** @var TaskHandler[] */
    private $particleTaskHandlers = [];

    /** @var int[][] */
    private $colors = [];


    public function __construct(Plugin $owner) {
        parent::__construct("particletest", $owner);
        $this->setExecutor($this);
        for ($g = 0; $g < 255; $g++) {
            $this->colors[] = [255, $g, 0];
        }
        for ($r = 255; $r > 0; $r--) {
            $this->colors[] = [$r, 255, 0];
        }
        for ($b = 0; $b < 255; $b++) {
            $this->colors[] = [0, 255, $b];
        }
        for ($g = 255; $g > 0; $g--) {
            $this->colors[] = [0, $g, 255];
        }
        for ($r = 0; $r < 255; $r++) {
            $this->colors[] = [$r, 0, 255];
        }
        for ($b = 255; $b > 0; $b--) {
            $this->colors[] = [255, 0, $b];
        }

    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if (!($sender instanceof Player)) {
            $sender->sendMessage(TextFormat::RED . "プレイヤー以外で実行してくるな！！");
            return true;
        }
        if (array_key_exists($sender->getName(), $this->particleTaskHandlers)) {
            unset($this->particleTaskHandlers[$sender->getName()]);
            $sender->sendMessage(TextFormat::RED . "offにしました");
            return true;
        } else {
            $color = 0;
            $handler = $this->getPlugin()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (int $tick) use ($sender, &$handler, &$color): void {
                if (!$sender->isOnline() || !array_key_exists($sender->getName(), $this->particleTaskHandlers)) {
                    $handler->cancel();
                    return;
                }
                $o = $sender->add(0, 1, 0);
                if(!array_key_exists($color,$this->colors)){
                    $color = 0;
                }
                $rgb = $this->colors[$color];
                $color++;
                $particle = new DustParticle($o, $rgb[0],$rgb[1],$rgb[2]);
                $level = $sender->getLevel();
                for ($rad = 0; $rad < M_PI * 2; $rad += M_PI / 4) {
                    $pos = $o->add(sin($rad), 0, cos($rad));
                    $level->addParticle($particle->setComponents($pos->x, $pos->y, $pos->z));
                }
            }), 1);

            $this->particleTaskHandlers[$sender->getName()] = $handler;
            $sender->sendMessage(TextFormat::GREEN . "onにしました");
            return true;

        }

    }
}