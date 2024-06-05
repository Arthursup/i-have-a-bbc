<?php

namespace learxd\potion;

use learxd\potion\utils\Utils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\item\ItemIds;
use pocketmine\item\Item;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;

class StackPotionHandler implements Listener {

    public function onPlayerConsume(PlayerItemConsumeEvent $consumeEvent) {
        $player = $consumeEvent->getPlayer();
        $item = $consumeEvent->getItem();
        if ($item->getId() === ItemIds::POTION) {
            if (!$consumeEvent->isCancelled()) {
                $consumeEvent->setCancelled(true);
                $player->getInventory()->removeItem($item);
                $this->applyPotionEffects($player, $item);
                $this->handlePotionStack($player, $item);
            }
        }
    }

    public function onPlayerInteract(PlayerInteractEvent $interactEvent) {
        $player = $interactEvent->getPlayer();
        $item = $interactEvent->getItem();
        list($id, $meta) = explode(':', Loader::get()->getConfig()->get('stack-item')['item']);
        if ($item->getId() == $id && $meta == $item->getMeta()) {
            if (Utils::stackPotions($player)) {
                $player->getInventory()->removeItem($item);
            }
        }
    }

    public function onPlayerHeldItem(PlayerItemHeldEvent $event) {
        $player = $event->getPlayer();
        $item = $event->getItem();
        if ($item->getId() === ItemIds::POTION && $item->getCustomName() == "") {
            if (isset(Loader::get()->getConfig()->get('custom-potions')[$item->getMeta()])) {
                $player->getInventory()->removeItem($item);
                $customName = $this->generateCustomPotionName($item->getMeta());
                $item->setCustomName($customName);
                $player->getInventory()->addItem($item);
            }
        }
    }

    private function applyPotionEffects($player, $item) {
        if (isset(Loader::get()->getConfig()->get('custom-potions')[$item->getMeta()])) {
            foreach (Loader::get()->getConfig()->get('custom-potions')[$item->getMeta()]['effects'] as $data) {
                $player->getEffects()->add(new EffectInstance(VanillaEffects::fromString($data['effect']), $data['duration'] * 20, $data['amplifier']));
            }
        } else {
            foreach ($item->getAdditionalEffects() as $effect) {
                $player->getEffects()->add($effect);
            }
        }
    }

    private function handlePotionStack($player, $item) {
        if (($potions = ($nbt = $item->getNamedTag())->getInt('PotionStack', 0)) > 1) {
            if (($potions - 1) > 0) {
                $nbt->setInt('PotionStack', $potions - 1);
                $item->setNamedTag($nbt);
                $item->setLore(
                    [
                        "",
                        "§7Poções stackadas: §f" . ($potions - 1),
                        "§7Cada vez que você tomar, esse número diminuirá!"
                    ]);
                $player->getInventory()->addItem($item);
                $player->sendMessage('§eVocê tomou uma das §7' . $potions . " " . $item->getName() . "§e stackadas! Você agora possuí: §7" . ($potions - 1) . "§e...");
            } else {
                $player->getInventory()->addItem(Item::get(ItemIds::GLASS_BOTTLE));
            }
        }
    }

    private function generateCustomPotionName($meta) {
        $potion = Loader::get()->getConfig()->get('custom-potions')[$meta];
        $customName = $potion['displayName'];
        foreach ($potion['effects'] as $data) {
            $customName .= "\n§r§7" . $data['name'] . " " . $data['amplifier'] . " (" . date('i:s', $data['duration']) . ")";
        }
        return $customName;
    }
}


5. **Correções no arquivo Utils.php:**

php
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

        array_walk($contents, function ($item, $slot) use (&$potions) {
            if ($item->getId() === ItemIds::POTION) {
                if (!isset($potions[$item->getMeta()])) {
                    $potions[$item->getMeta()] = 0;
                }
                $potions[$item->getMeta()] += $item->getCount();
            }
        });

        foreach ($potions as $meta => $count) {
            $stackSize = min($count, 64);
            $nbt = Item::get(ItemIds::POTION, $meta, $stackSize)->getNamedTag();
            $nbt->setInt('PotionStack', $count);
            $potionItem = Item::get(ItemIds::POTION, $meta, 1);
            $potionItem->setNamedTag($nbt);
            $potionItem->setLore(
                [
                    "",
                    "§7Poções stackadas: §f" . $count,
                    "§7Cada vez que você tomar, esse número diminuirá!"
                ]);
            $player->getInventory()->addItem($potionItem);
        }

        return true;
    }
}


