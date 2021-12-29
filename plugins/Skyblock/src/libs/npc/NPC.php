<?php

namespace libs\npc;

use pocketmine\entity\Entity;

use pocketmine\Player;

use NpcDialog\NpcDialog;
use NpcDialog\Button;
use NpcDialog\DialogForm;
use NpcDialog\DialogFormStore;
use RedCraftPE\RedSkyBlock\SkyBlock;
use jojoe77777\FormAPI\ModalForm;
use pocketmine\entity\Villager;
Class NPC{

    public function __construct(){
       // NpcDialog::register(Skyblock::getInstance());
        //Entity::registerEntity(CTVillager::class, true);
    }

    public function spawn(Player $player){
        $nbt = Entity::createBaseNBT($player, null, $player->yaw, $player->pitch);
        $entity = Entity::createEntity("Villager", $player->level, $nbt);
        $entity->spawnToAll();
        $entity->setNameTag("NPC!");
        $form = $this->getForm($player);
		$form->setCloseListener(function(Player $player)use($form, $entity){
			//DialogFormStore::unregisterForm($form);
			//$newform = $this->getForm($player);
			$form->pairWithEntity($entity);
		});
        $form->pairWithEntity($entity);
    }

    public function getForm(Player $player){
        $skyblockArray = SkyBlock::getInstance()->skyblock->get("SkyBlock", []);
        $senderName = strtolower($player->getName());
        $form = new DialogForm("....");
        if(array_key_exists($senderName, $skyblockArray)){
            $form = new DialogForm("Có đảo rồi lại đây chi nữa 3");
        }else{
            $form = new DialogForm("Hình như ngươi chưa có nơi để cư trú nhỉ, tìm ta là đúng rồi đấy, ta có vài mẫu đảo cho ngươi chọn đây");
            $islands = Skyblock::getInstance()->getIslands();
            foreach($islands as $is){
                $form->addButton(new Button(explode(".", $is)[0], function(Player $player) use ($is){
                    $form = new ModalForm(function(Player $player, ?bool $data) use ($is){
                        if(is_null($data)) return;
                        if($data){
                            SkyBlock::getInstance()->getServer()->dispatchCommand($player, "is create ". explode(".", $is)[0]);
                        }
                    });
                    $form->setTitle(explode(".", $is)[0]);
                    $form->setContent(SkyBlock::getInstance()->getDesIsland($is));
                    $form->setButton1("Tôi sẽ nhận đảo này");
                    $form->setButton2("Không, tôi sẽ chọn đảo khác");
                    $form->sendToPlayer($player);
                }));
            }            
        }
		
        return $form;
    }
}