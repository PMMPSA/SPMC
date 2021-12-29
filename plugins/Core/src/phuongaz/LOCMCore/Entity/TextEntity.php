<?php

namespace phuongaz\LOCMCore\Entity;

use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use phuongaz\LOCMCore\Form\TutorialForm;
class TextEntity extends Human{


    protected function initEntity() : void{
	    parent::initEntity();
    }

    public function hasMovementUpdate() : bool{
        return false;
    }

    public function attack(EntityDamageEvent $source) : void{
        $source->setCancelled();
        $form = new TutorialForm();
		if($source instanceof EntityDamageByEntityEvent){
			$dmg = $source->getDamager();
			if($dmg instanceof Player){
				parent::attack($source);
				if($dmg->getName() == "phuongaz" or $dmg->isOp() and $dmg->getInventory()->getItemInHand()->getId() == 1){
					$this->kill();
				}elseif($dmg->getName() == "phuongaz" and $dmg->getInventory()->getItemInHand()->getId() == 4){
					$this->yaw += 3;
				}
				$form->send($dmg);
			}
        }
		parent::attack($source);
    }

	public function onUpdate(int $currentTick): bool {
		if($this->getLevel()->getServer()->getTick() % 30 == 0){
			$names = ["§l§fChào mừng người chơi", "§l§aNạp thẻ để duy trì máy chủ"];
            $this->setNameTag("§l§6LOCM§a SKYBLOCK" . "\n" . $names[array_rand($names)]);
		}
		if($this->getLevel()->getServer()->getTick() % 50 == 0){
			$this->setScale(3);
		}
		//$this->yaw = $this->yaw + 3;
		$this->updateMovement();
		parent::onUpdate($currentTick);
		return !$this->closed;
	}
}