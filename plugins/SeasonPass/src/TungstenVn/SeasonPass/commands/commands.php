<?php
namespace TungstenVn\SeasonPass\commands;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\event\Listener;


use TungstenVn\SeasonPass\SeasonPass;
use TungstenVn\SeasonPass\subCommands\addItem;
use TungstenVn\SeasonPass\menuHandle\menuHandle;
use TungstenVn\SeasonPass\subCommands\removeItem;
use TungstenVn\SeasonPass\subCommands\setItemInfo;

use TungstenVn\SeasonPass\libs\jojoe77777\FormAPI\SimpleForm;
class commands extends Command implements PluginIdentifiableCommand, Listener
{

    /*  Main Class (SeasonPass) */
    public $ssp;

    public function __construct(SeasonPass $ssp)
    {
        parent::__construct("ssp", "SeasonPass Commands", ("/ssp help"), []);
        $this->ssp = $ssp;
    }

    public function execute(CommandSender $sender, $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            if (!isset($args[0])) {
                $a = new menuHandle($this, $sender);
                $this->ssp->getServer()->getPluginManager()->registerEvents($a, $this->ssp);
                return;
            }
            switch ($args[0]) {
                case 'a':
                case 'additem':
                    new addItem($this, $sender, $args);
                    break;
                case 'sl':
                case 'setlore':
                    new setItemInfo(1, $sender, $args);
                    break;
                case 'sn':
                case 'setname':
                    new setItemInfo(0, $sender, $args);
                    break;
                case 'r':
                case 'removeitem':
                    new removeItem($this, $sender, $args);
                    break;
                default:
                    $this->helpForm($sender,"");
                    break;
            }
        } else {
            $sender->sendMessage("Please run command in-game.");
        }
    }
    public function helpForm(Player $player,string $txt){
        $form = new SimpleForm(function(Player $player, int $data = null) {
            $result = $data;
            if ($result === null) {
                return;
            }
            switch ($result) {
                case 0:
                    $player->sendMessage("§eHave a good day!");
                    break;
                case 1:
                    $this->helpForm($player,"§cIf you dont get it, read it again:\n");
                    break;
                default:
                    break;
            }
        });
        $form->setTitle("§eSeasonPass §bHelp");
        $form->setContent($txt."Use §e/ssp§r to open SeasonPass.\nUsing SeasonPass just like how you use Royal Pass on PUBG,CSGO,...\n\nWith §fnormalPass§r, everyone can claim an item when they has enough level,however, §cRoyalPass§r is not for everyone, it's have more valueable items inside"
        );
        $form->addButton("I understood", 0, "textures/items/light_block_7");
        $form->addButton("I dont get it", 0, "textures/items/light_block_0");
        $player->sendForm($form);
        return $form;
    }
    public function getPlugin(): Plugin
    {
        return $this->ssp;
    }
}
