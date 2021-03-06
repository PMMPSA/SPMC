<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyCustomEnchants\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyCustomEnchants\CustomEnchantManager;
use DaPigGuy\PiggyCustomEnchants\PiggyCustomEnchants;
use DaPigGuy\PiggyCustomEnchants\utils\Utils;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class RemoveSubCommand extends BaseSubCommand
{
    /** @var PiggyCustomEnchants */
    protected $plugin;

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($sender instanceof Player && PiggyCustomEnchants::getInstance()->areFormsEnabled() && !isset($args["enchantment"])) {
            $this->onRunForm($sender, $aliasUsed, $args);
            return;
        }

        if ((!$sender instanceof Player && empty($args["player"])) || !isset($args["enchantment"])) {
            $sender->sendMessage("Usage: /ce remove <enchantment> <player>");
            return;
        }
        $target = empty($args["player"]) ? $sender : PiggyCustomEnchants::getInstance()->getServer()->getPlayer($args["player"]);
        if (!$target instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "Invalid player.");
            return;
        }
        $enchant = CustomEnchantManager::getEnchantmentByName($args["enchantment"]);
        if ($enchant === null) {
            $sender->sendMessage(TextFormat::RED . "Invalid enchantment.");
            return;
        }
        $item = $target->getInventory()->getItemInHand();
        if ($item->getEnchantment($enchant->getId()) === null) {
            $sender->sendMessage(TextFormat::RED . "Item does not have specified enchantment.");
            return;
        }
        $item->removeEnchantment($enchant->getId());
        if (count($item->getEnchantments()) === 0) $item->removeEnchantments();
        $sender->sendMessage(TextFormat::GREEN . "Enchantment successfully removed.");
        $target->getInventory()->setItemInHand($item);
    }

    public function onRunForm(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($sender instanceof Player) {
            $form = new CustomForm(function (Player $player, ?array $data): void {
                if ($data !== null) {
                    $enchant = is_numeric($data[0]) ? CustomEnchantManager::getEnchantment((int)$data[0]) : CustomEnchantManager::getEnchantmentByName($data[0]);
                    if ($enchant == null) {
                        Utils::errorForm($player, TextFormat::RED . "Invalid enchantment.");
                        return;
                    }
                    $target = PiggyCustomEnchants::getInstance()->getServer()->getPlayer($data[1]);
                    if (!$target instanceof Player) {
                        Utils::errorForm($player, TextFormat::RED . "Invalid player.");
                        return;
                    }
                    $item = $target->getInventory()->getItemInHand();
                    if ($item->getEnchantment($enchant->getId()) === null) {
                        $player->sendMessage(TextFormat::RED . "Item does not have specified enchantment.");
                        return;
                    }
                    $item->removeEnchantment($enchant->getId());
                    if (count($item->getEnchantments()) === 0) $item->removeEnchantments();
                    $target->sendMessage(TextFormat::GREEN . "Enchantment successfully removed.");
                    $target->getInventory()->setItemInHand($item);
                }
            });
            $form->setTitle(TextFormat::GREEN . "Remove Custom Enchantment");
            $form->addInput("Enchantment");
            $form->addInput("Player", "", $sender->getName());
            $sender->sendForm($form);
        }
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->setPermission("piggycustomenchants.command.ce.remove");
        $this->registerArgument(0, new RawStringArgument("enchantment", true));
        $this->registerArgument(1, new RawStringArgument("player", true));
    }
}