<?php

namespace learxd\potion\commands;

use learxd\potion\Loader;
use learxd\potion\utils\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class StackPotionsCommand extends Command {

    public function __construct() {
        parent::__construct("stackpotions", "Stacke as poções em seu inventário em uma só...", "", ["stack"]);
        $permission = Loader::get()->getConfig()->get('stack-permission', '');
        if ($permission !== '') {
            $this->setPermission($permission);
        }
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if ($sender instanceof Player && $this->testPermission($sender)) {
            $sender->sendMessage("§eComprimindo poções...");
            Utils::stackPotions($sender);
        }
    }
}
