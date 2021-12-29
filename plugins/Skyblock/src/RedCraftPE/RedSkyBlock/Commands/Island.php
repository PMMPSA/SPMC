<?php
declare(strict_types=1);
namespace RedCraftPE\RedSkyBlock\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use RedCraftPE\RedSkyBlock\SkyBlock;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Add;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Ban;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Challenges;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Claim;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Create;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\CreateWorld;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Custom;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Decrease;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Delete;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Fly;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Help;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Hunger;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Increase;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Info;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Kick;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Leave;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Lock;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\MakeSpawn;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Members;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Name;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\On;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Pos1;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Pos2;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Rank;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Reload;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Remove;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Reset;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Set;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\SetSpawn;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Settings;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\SetWorld;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Teleport;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Top;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Unban;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\Unlock;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\VoidClass;
use RedCraftPE\RedSkyBlock\Commands\SubCommands\NewIsland;
use libs\SkyBlockForm;
class Island {

  private static $instance;

  private $add;
  private $ban;
  private $challenges;
  private $claim;
  private $create;
  private $createWorld;
  private $custom;
  private $decrease;
  private $delete;
  private $fly;
  private $help;
  private $hunger;
  private $increase;
  private $info;
  private $kick;
  private $leave;
  private $lock;
  private $makeSpawn;
  private $members;
  private $name;
  private $on;
  private $pos1;
  private $pos2;
  private $rank;
  private $reload;
  private $remove;
  private $reset;
  private $set;
  private $setSpawn;
  private $settings;
  private $setWorld;
  private $teleport;
  private $top;
  private $unban;
  private $unlock;
  private $void;
  private $newisland;

  public function __construct($plugin) {

    self::$instance = $this;

    $this->plugin = $plugin;

    $this->add = new Add();
    $this->ban = new Ban();
    $this->challenges = new Challenges();
    $this->claim = new Claim();
    $this->create = new Create($this->plugin);
    $this->createWorld = new CreateWorld($this->plugin);
    $this->custom = new Custom();
    $this->decrease = new Decrease();
    $this->delete = new Delete();
    $this->fly = new Fly();
    $this->help = new Help();
    $this->hunger = new Hunger();
    $this->increase = new Increase();
    $this->info = new Info();
    $this->kick = new Kick();
    $this->leave = new Leave();
    $this->lock = new Lock();
    $this->makeSpawn = new MakeSpawn();
    $this->members = new Members();
    $this->name = new Name();
    $this->on = new On();
    $this->pos1 = new Pos1();
    $this->pos2 = new Pos2();
    $this->rank = new Rank();
    $this->reload = new Reload();
    $this->remove = new Remove();
    $this->reset = new Reset($this->plugin);
    $this->set = new Set();
    $this->setSpawn = new SetSpawn();
    $this->settings = new Settings();
    $this->setWorld = new SetWorld();
    $this->teleport = new Teleport();
    $this->top = new Top();
    $this->unban = new Unban();
    $this->unlock = new Unlock();
    $this->void = new VoidClass();
    $this->newisland = new NewIsland();
  }
  public function onIslandCommand(CommandSender $sender, Command $command, string $label, array $args): bool {

    if ($sender->hasPermission("skyblock.is")) {

      if (!$args) {
        if($sender instanceof Player){
          $form = new SkyBlockForm();
          $form->onForm($sender);          
        }
        return true;
        //return $this->help->onHelpCommand($sender, $args);
      } else {

        switch (strtolower($args[0])) {
          case "newisland":
          case "ni":
            return $this->newisland->onNewIslandCommand($sender, $args);
            break;
          case "add":

            return $this->add->onAddCommand($sender, $args);
          break;
          case "ban":
          case "expel":

            return $this->ban->onBanCommand($sender, $args);
          break;
          case "challenges":
          case "challenge":

            return $this->challenges->onChallengeCommand($sender, $args);
          break;
          case "claim":
          case "complete":

            return $this->claim->onClaimCommand($sender, $args);
          break;
          case "create":
            return $this->create->onCreateCommand($sender, $args);
          break;
          case "cw":
          case "createworld":

            return $this->createWorld->onCreateWorldCommand($sender, $args);
          break;
          case "custom":

            return $this->custom->onCustomCommand($sender, $args);
          break;
          case "decrease":

            return $this->decrease->onDecreaseCommand($sender, $args);
          break;
          case "delete":

            return $this->delete->onDeleteCommand($sender, $args);
          break;
          case "fly":

            return $this->fly->onFlyCommand($sender);
          break;
          case "help":

            return $this->help->onHelpCommand($sender, $args);
          break;
          case "hunger":

            return $this->hunger->onHungerCommand($sender, $args);
          break;
          case "increase":

            return $this->increase->onIncreaseCommand($sender, $args);
          break;
          case "info":

            return $this->info->onInfoCommand($sender, $args);
          break;
          case "kick":

            return $this->kick->onKickCommand($sender, $args);
          break;
          case "leave":

            return $this->leave->onLeaveCommand($sender, $args);
          break;
          case "close":
          case "lock":

            return $this->lock->onLockCommand($sender);
          break;
          case "makespawn":
          case "createspawn":

            return $this->makeSpawn->onMakeSpawnCommand($sender);
          break;
          case "members":

            return $this->members->onMembersCommand($sender);
          break;
          case "rename":
          case "name":

            return $this->name->onNameCommand($sender, $args);
          break;
          case "on":

            return $this->on->onOnCommand($sender);
          break;
          case "pos1":

            return $this->pos1->onPos1Command($sender);
          break;
          case "pos2":

            return $this->pos2->onPos2Command($sender);
          break;
          case "rank":

            return $this->rank->onRankCommand($sender);
          break;
          case "load":
          case "reload":

            return $this->reload->onReloadCommand($sender);
          break;
          case "remove":

            return $this->remove->onRemoveCommand($sender, $args);
          break;
          case "restart":
          case "reset":

            return $this->reset->onResetCommand($sender);
          break;
          case "sw":
          case "setworld":

            return $this->setWorld->onSetWorldCommand($sender);
          break;
          case "settings":

            return $this->settings->onSettingsCommand($sender, $args);
          break;
          case "set":

            return $this->set->onSetCommand($sender);
          break;
          case "setspawn":

            return $this->setSpawn->onSetSpawnCommand($sender);
          break;
          case "spawn":
          case "goto":
          case "go":
          case "tp":
          case "teleport":
          case "visit":

            return $this->teleport->onTeleportCommand($sender, $args);
          break;
          case "lb":
          case "leaderboard":
          case "top":

            return $this->top->onTopCommand($sender);
          break;
          case "unban":
          case "pardon":

            return $this->unban->onUnbanCommand($sender, $args);
          break;
          case "open":
          case "unlock":

            return $this->unlock->onUnlockCommand($sender);
          break;
          case "void":

            return $this->void->onVoidCommand($sender, $args);
          break;
        }
        return false;
      }
    } else {

      $sender->sendMessage(TextFormat::RED . "You do not have the proper permissions to run this command.");
      return true;
    }
  }
}
