<?php






/*
remake by phuongaz
code form Teaspoon sources
*/
namespace vanilla;

use pocketmine\Player;

use pocketmine\plugin\PluginBase;

use pocketmine\block\Block;

use pocketmine\item\Item;
use pocketmine\item\Sword;
use pocketmine\item\ItemFactory;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;

use pocketmine\event\Listener;

use pocketmine\event\block\BlockBreakEvent;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityShootBowEvent;

use vanilla\item\EnchantedBook;

use pocketmine\item\Pickaxe;
use pocketmine\item\Axe;
use pocketmine\item\TieredTool;
class Core extends PluginBase implements Listener{
	
	public const UNDEAD = [
		Entity::ZOMBIE,
		Entity::HUSK,
		Entity::WITHER,
		Entity::SKELETON,
		Entity::STRAY,
		Entity::WITHER_SKELETON,
		Entity::ZOMBIE_PIGMAN,
		Entity::ZOMBIE_VILLAGER
	];
	
	public const ARTHROPODS = [
		Entity::SPIDER,
		Entity::CAVE_SPIDER,
		Entity::SILVERFISH,
		Entity::ENDERMITE
	];
	

	
	public function onLoad(){
			
		$this->getLogger()->info("Registering enchantments and enchanted books...");
			
		$this->registerTypes();
			
		ItemFactory::registerItem(new EnchantedBook(), true);
		Item::initCreativeItems();
			
	}
	
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("Vanilla enchantments were successfully registered, plugin remake by phuongaz");
	}

	
	public function registerTypes() : void{
		// some enchabtments are mit done and some will be removed in future
		Enchantment::registerEnchantment(new Enchantment(Enchantment::DEPTH_STRIDER, "Depth strider", Enchantment::RARITY_UNCOMMON, Enchantment::SLOT_FEET, Enchantment::SLOT_AXE, 3));
		Enchantment::registerEnchantment(new Enchantment(Enchantment::AQUA_AFFINITY, "Aqua affinity", Enchantment::RARITY_UNCOMMON, Enchantment::SLOT_HEAD, Enchantment::SLOT_AXE, 1));
		
		Enchantment::registerEnchantment(new Enchantment(Enchantment::SMITE, "Smite", Enchantment::RARITY_UNCOMMON, Enchantment::SLOT_SWORD, Enchantment::SLOT_AXE, 5));
		Enchantment::registerEnchantment(new Enchantment(Enchantment::BANE_OF_ARTHROPODS, "Bane of arthropods", Enchantment::RARITY_UNCOMMON, Enchantment::SLOT_SWORD, Enchantment::SLOT_AXE, 5));
		
		Enchantment::registerEnchantment(new Enchantment(Enchantment::UNBREAKING, "%enchantment.durability", Enchantment::RARITY_UNCOMMON, Enchantment::SLOT_DIG | Enchantment::SLOT_ARMOR | Enchantment::SLOT_FISHING_ROD | Enchantment::SLOT_BOW | Enchantment::SLOT_SWORD, Enchantment::SLOT_TOOL | Enchantment::SLOT_CARROT_STICK | Enchantment::SLOT_ELYTRA, 3));
		Enchantment::registerEnchantment(new Enchantment(Enchantment::LOOTING, "Looting", Enchantment::RARITY_UNCOMMON, Enchantment::SLOT_SWORD, Enchantment::SLOT_NONE, 3));
		Enchantment::registerEnchantment(new Enchantment(Enchantment::FORTUNE, "Fortune", Enchantment::RARITY_UNCOMMON, Enchantment::SLOT_DIG, Enchantment::SLOT_NONE, 3));
	}
	
	/**
	 * @param BlockBreakEvent $event
	 * @param ignoreCancelled true
	 * @priority LOWEST
	 */
	
	public function onBreak(BlockBreakEvent $ev){
		$block = $ev->getBlock();
		$item = $ev->getItem();
		
		if(($fortuneEnchantment = $item->getEnchantment(Enchantment::FORTUNE)) instanceof EnchantmentInstance){
			$level = $fortuneEnchantment->getLevel() + 1;
			$rand = rand(1, $level);
			if($item instanceof TieredTool){
				switch($block->getId()){
					case Block::COAL_ORE:
						if($item instanceof Pickaxe){
							$ev->setDrops($this->increaseDrops($ev->getDrops(), $rand));
						}
						break;
					case Block::LAPIS_ORE:
						if($item instanceof Pickaxe && $item->getTier() > TieredTool::TIER_WOODEN){
							$ev->setDrops($this->increaseDrops($ev->getDrops(), rand(0, 3) + $rand));
						}
						break;
					case Block::GLOWING_REDSTONE_ORE:
					case Block::REDSTONE_ORE:
						if($item instanceof Pickaxe && $item->getTier() > TieredTool::TIER_WOODEN){
							$ev->setDrops($this->increaseDrops($ev->getDrops(), rand(1, 2) + $rand));
						}
						break;
					case Block::NETHER_QUARTZ_ORE:
						if($item instanceof Pickaxe && $item->getTier() > TieredTool::TIER_WOODEN){
							$ev->setDrops($this->increaseDrops($ev->getDrops(), rand(0, 1) + $rand));
						}
						break;
					case Block::DIAMOND_ORE:
						if($item instanceof Pickaxe && $item->getTier() >= TieredTool::TIER_IRON){
							$ev->setDrops($this->increaseDrops($ev->getDrops(), $rand));
						}
						break;
					case Block::EMERALD_ORE:
						if($item instanceof Pickaxe && $item->getTier() >= TieredTool::TIER_IRON){
							$ev->setDrops($this->increaseDrops($ev->getDrops(), $rand));
						}
						break;
					case Block::CARROT_BLOCK:
					case Block::POTATO_BLOCK:
					case Block::BEETROOT_BLOCK:
					case Block::WHEAT_BLOCK:
						if($item instanceof Axe || $item instanceof Pickaxe){
							if($block->getDamage() >= 7){
								$ev->setDrops($this->increaseDrops($ev->getDrops(), rand(1, 3) + $rand));
							}
						}
						break;
					case Block::MELON_BLOCK:
						if($item instanceof Axe || $item instanceof Pickaxe){
							$ev->setDrops($this->increaseDrops($ev->getDrops(), rand(3, 9) + $rand));
						}
						break;
					case Block::LEAVES:
						if(rand(1, 100) <= 10 + $level * 2){
							$ev->setDrops([Item::get(Item::APPLE, 0, 1)]);
						}
						break;
				}
			}
		}
	}

	private function increaseDrops(array $drops, int $amount = 1) {
		$newDrops = [];
		foreach($drops as $drop){
			$newDrops[] = $drop->setCount(1 + $amount);
		}
		return $newDrops;
	}

	
	/**
	 * @param EntityDamageByEntityEvent $event
	 * @ignoreCancelled true
	 * @priority LOWEST
	 */
	
	/** @var string */
	public const BANE_OF_ARTHROPODS_AFFECTED_ENTITIES = [ // Based on https://minecraft.gamepedia.com/Enchanting#Bane_of_Arthropods ^_^
		"Spider", "Cave Spider",
		"Silverfish", "Endermite",
	];

	/**
	 * @param EntityDamageEvent $ev
	 *
	 * @priority LOWEST
	 * @ignoreCancelled true
	 */
	public function onDamage(EntityDamageEvent $ev){
		$e = $ev->getEntity();
		if($ev instanceof EntityDamageByEntityEvent){
			$d = $ev->getDamager();
			if(!($d instanceof Entity) || !$d->isAlive()){
				return;
			}
			if($d instanceof PMPlayer && $e instanceof Living){
				$i = $d->getInventory()->getItemInHand();
				$damage = $ev->getModifier(EntityDamageEvent::MODIFIER_ARMOR);
				foreach(Utils::getEnchantments($i) as $ench){
					$lvl = $ench->getLevel();
					switch($ench->getId()){
						case Enchantment::BANE_OF_ARTHROPODS:
							if(Utils::in_arrayi($e->getName(), self::BANE_OF_ARTHROPODS_AFFECTED_ENTITIES)){
								$ev->setModifier($damage + ($lvl * 2.5), EntityDamageEvent::MODIFIER_ARMOR);
							}
							break;
						case Enchantment::SMITE:
							if($e instanceof Undead || $e instanceof Zombie){
								$ev->setModifier($damage + ($lvl * 2.5), EntityDamageEvent::MODIFIER_ARMOR);
							}
							break;
					}
				}
			}
		}
	}
	
	/**
	 * @param array $drops
	 * @param array $items
	 * @param int $add
	 * @return array
	 */
	
	public function getLootingDrops(array $drops, array $items, int $add) : array{
		$r = [];
		
		foreach($items as $ite){
			$item = Item::fromString($ite);
			
			foreach($drops as $drop){
				if($drop->getId() == $item->getId()){
					$drop->setCount($drop->getCount() + $add);
				}
				
				$r[] = $drop;
				break;
			}
		}
		
		return $r;
	}
	
	/**
	 * @param EntityShootBowEvent $event
	 * @ignoreCancelled true
	 * @priority LOWEST
	 */
	
	public function onShoot(EntityShootBowEvent $event) : void{
		$arrow = $event->getProjectile();
		$bow = $event->getBow();
		
		if($arrow !== null and $arrow::NETWORK_ID == Entity::ARROW){
			$event->setForce($event->getForce() + 0.95); // In vanilla, arrows are fast
		}
	}

		/**
	 * @param EntityDeathEvent $ev
	 *
	 * @priority LOWEST
	 * @ignoreCancelled true
	 */
	public function onEntityDeath(EntityDeathEvent $ev){
		$ent = $ev->getEntity();
		if(!($ent instanceof Human)){
			$cause = $ent->getLastDamageCause();
			if($cause instanceof EntityDamageByEntityEvent){
				$damager = $cause->getDamager();
				if($damager instanceof PMPlayer){
					$enchantment = $damager->getInventory()->getItemInHand()->getEnchantment(Enchantment::LOOTING);
					if($enchantment instanceof EnchantmentInstance){
						$ev->setDrops($this->increaseDrops($ev->getDrops(), rand(1, $enchantment->getLevel() + 1)));
					}
				}
			}
		}
	}
}