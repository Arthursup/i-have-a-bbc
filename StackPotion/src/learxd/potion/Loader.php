<?php

namespace learxd\potion;

use learxd\potion\commands\StackPotionsCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

class Loader extends PluginBase implements Listener {

    protected static $instance;

    public static function get(): Loader {
        return self::$instance;
    }

    public function onEnable(): void {
        self::$instance = $this;
        $this->saveResource('config.yml', false);
        $this->getServer()->getCommandMap()->register('stackpotion', new StackPotionsCommand());
        $this->getServer()->getPluginManager()->registerEvents(new StackPotionHandler(), $this);
    }
}
