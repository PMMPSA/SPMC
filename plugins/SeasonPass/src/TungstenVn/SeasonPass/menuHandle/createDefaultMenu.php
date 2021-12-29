<?php

namespace TungstenVn\SeasonPass\menuHandle;

use pocketmine\item\Item;

use TungstenVn\SeasonPass\menuHandle\menuHandle;

use TungstenVn\SeasonPass\libs\muqsit\invmenu\InvMenu;
use TungstenVn\SeasonPass\libs\muqsit\invmenu\SharedInvMenu;
class createDefaultMenu
{
    public $menu;

    public function __construct(menuHandle $mnh, $sender)
    {
        $this->createMenu($sender);
    }

    public function createMenu($sender)
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST)
            ->setName("§0LOCM §bPass");
        $normalBook = Item::get(340, 0, 1)->setCustomName("§rNormal Pass");
        $normalBook->setLore(["§r§fEveryone can claim item in this pass"]);

        $royalBook = Item::get(387, 0, 1)->setCustomName("§rRoyal Pass");
        $royalBook->setLore(["§r§cBuy §fthis pass to claim item"]);

        $menu->getInventory()->setItem(0, $normalBook);
        $menu->getInventory()->setItem(1, Item::get(160, 5, 1));
        $menu->getInventory()->setItem(10, Item::get(160, 5, 1));
        $menu->getInventory()->setItem(27, $royalBook);
        $menu->getInventory()->setItem(28, Item::get(160, 4, 1));
        $menu->getInventory()->setItem(37, Item::get(160, 4, 1));

        $menu->getInventory()->setItem(45, Item::get(339, 0, 1)->setCustomName("§rLeft"));
        $menu->getInventory()->setItem(53, Item::get(339, 0, 1)->setCustomName("§rRight"));

        $menu->getInventory()->setItem(18, Item::get(399, 0, 1));
        $menu->getInventory()->setItem(19, Item::get(399, 0, 1));
        $this->menu = $menu;
    }
}