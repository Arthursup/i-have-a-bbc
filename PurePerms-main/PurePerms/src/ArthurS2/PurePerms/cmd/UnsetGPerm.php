<?php

namespace _64FF00\PurePerms\cmd;

use _64FF00\PurePerms\PurePerms;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class UnsetGPerm extends Command{
	/*
		PurePerms by 64FF00 (Twitter: @64FF00)

		  888  888    .d8888b.      d8888  8888888888 8888888888 .d8888b.   .d8888b.
		  888  888   d88P  Y88b    d8P888  888        888       d88P  Y88b d88P  Y88b
		888888888888 888          d8P 888  888        888       888    888 888    888
		  888  888   888d888b.   d8P  888  8888888    8888888   888    888 888    888
		  888  888   888P "Y88b d88   888  888        888       888    888 888    888
		888888888888 888    888 8888888888 888        888       888    888 888    888
		  888  888   Y88b  d88P       888  888        888       Y88b  d88P Y88b  d88P
		  888  888    "Y8888P"        888  888        888        "Y8888P"   "Y8888P"
	*/

	private $plugin;

	/**
	 * @param PurePerms $plugin
	 * @param           $name
	 * @param           $description
	 */
	public function __construct(PurePerms $plugin, $name, $description){
		$this->plugin = $plugin;

		parent::__construct($name, $description);

		$this->setPermission("pperms.command.unsetgperm");
	}

    /**
     * @param CommandSender $sender
     * @param string $label
     * @param array $args
     *
     * @return bool
     */
	public function execute(CommandSender $sender, string $label, array $args) : bool{
		if(!$this->testPermission($sender)){
			return false;
		}

		if(count($args) < 2 || count($args) > 3){
			$sender->sendMessage(TextFormat::GREEN . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.unsetgperm.usage"));

			return true;
		}

		$group = $this->plugin->getGroup($args[0]);

		if($group == null){
			$sender->sendMessage(TextFormat::RED . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.unsetgperm.messages.group_not_exist", [$args[0]]));

			return true;
		}

		$permission = $args[1];

		$levelName = null;

		if(isset($args[2])){
			$level = $this->plugin->getServer()->getWorldManager()->getWorldByName($args[2]);

			if($level == null){
				$sender->sendMessage(TextFormat::RED . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.unsetgperm.messages.level_not_exist", [$args[2]]));

				return true;
			}

			$levelName = $level->getFolderName();
		}

		$group->unsetGroupPermission($permission, $levelName);

		$sender->sendMessage(TextFormat::GREEN . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.unsetgperm.messages.gperm_removed_successfully", [$permission]));

		return true;
	}

	public function getPlugin() : Plugin{
		return $this->plugin;
	}
}