<?php

namespace phuongaz\Heaven\Events;

use pocketmine\event\Listener;
use phuongaz\Heaven\GemsMain;


use pocketmine\event\{
	inventory\InventoryTransactionEvent,
	player\PlayerInteractEvent
};

use DaPigGuy\PiggyCustomEnchants\utils\Utils;
use pocketmine\network\mcpe\protocol\InventorySlotPacket;
use pocketmine\inventory\transaction\action\SlotChangeAction;

use pocketmine\item\{Item, Armor, Sword, Tool};

Class EventListeners implements Listener 
{

	private $plugin; 

	public function __construct(GemsMain $main){
		$this->plugin = $main;
	}

    public function onPlayerInteract(\pocketmine\event\player\PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $level = $player->getLevel();
        $item = $event->getItem();
        if($item->getNamedTagEntry('Gems') !== null){
            $event->setCancelled();
        }
    }

	public function onInteract(PlayerInteractEvent $event){

	}

    public function onTransaction(InventoryTransactionEvent $event): void
    {
        $transaction = $event->getTransaction();
        $actions = $transaction->getActions();
        $oldToNew = isset(array_keys($actions)[0]) ? $actions[array_keys($actions)[0]] : null;
        $newToOld = isset(array_keys($actions)[1]) ? $actions[array_keys($actions)[1]] : null;
        if ($oldToNew instanceof SlotChangeAction && $newToOld instanceof SlotChangeAction) {
            $itemClicked = $newToOld->getSourceItem();
            $itemClickedWith = $oldToNew->getSourceItem();

            if ($itemClickedWith->getNamedTagEntry('Gems') !== null && $itemClicked->getId() !== Item::AIR) {

                if (count($itemClickedWith->getEnchantments()) < 1) return;
                $enchantmentSuccessful = false;
                foreach ($itemClickedWith->getEnchantments() as $enchantment) {
                    if($itemClicked instanceof Armor || $itemClicked instanceof Tool || $itemClicked instanceof Sword){
                      // if (!Utils::canBeEnchanted($itemClicked, $enchantment->getType(), $enchantment->getLevel())) continue;
                            $itemClicked->addEnchantment($enchantment);
                            $newToOld->getInventory()->setItem($newToOld->getSlot(), $itemClicked);
                            $enchantmentSuccessful = true;
                        }                        
                    }

                if ($enchantmentSuccessful) {
                    $event->setCancelled();
                    $oldToNew->getInventory()->setItem($oldToNew->getSlot(), Item::get(Item::AIR));
                }
            }
        }
    }
}