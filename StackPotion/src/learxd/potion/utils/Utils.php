<?php

namespace learxd\potion\utils;

use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class Utils {

    public static function itemsSerialize(array $contents) {
        array_walk($contents, function ($item, $slot) use (&$contents) {
            $contents[$slot] = $item->getId() . ":" . $item->getMeta();
        });
        return $contents;
    }

    public static function stackPotions(Player $player) {
        $contents = $player->getInventory()->getContents(true);

        $oldStack = [];
        $potions = [];

        array_walk($contents, function ($item, $slot
