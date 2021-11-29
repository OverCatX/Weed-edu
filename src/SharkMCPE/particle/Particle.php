<?php

namespace SharkMCPE\particle;

use pocketmine\entity\Entity;
use pocketmine\entity\EntityIds;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\Player;
use SharkMCPE\Guncha;
use SharkMCPE\task\CallBackTask;

class Particle
{

    private $plugin;

    public function __construct(Guncha $plugin)
    {
        $this->plugin = $plugin;
    }

    public function LootedParticle($player,$amount): void{
        $pk = new AddActorPacket();
        $eid = Entity::$entityCount++;
        $pk->entityRuntimeId = $eid;
        $pk->type = AddActorPacket::LEGACY_ID_MAP_BC[EntityIds::ITEM];
        $pk->position = $player->asVector3()->add(mt_rand(-10, 10) * 0.1, $player->getEyeHeight() / 2 + mt_rand(-7, 7) * 0.1, mt_rand(-10, 10) * 0.1);
        $pk->motion = new Vector3(0, 0.15, 0);
        $flags = 0;
        $flags |= 1 << Entity::DATA_FLAG_CAN_SHOW_NAMETAG;
        $text = ($player instanceof Player) ? "§f+ §l§2กัญ§aชา §6* §c".$amount : "§f+ §l§2กัญ§aชา §6* §c".$amount;
        $pk->metadata = [
            Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
            Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $text],
            Entity::DATA_ALWAYS_SHOW_NAMETAG => [Entity::DATA_TYPE_BYTE, 1],
            Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, 1]
        ];
        $player->dataPacket($pk);
        $this->plugin->getScheduler()->scheduleDelayedTask(new CallBackTask([$this, 'removeParticle'], [$player, $eid]), 20);
    }

    public function removeParticle($entity, int $eid): void{
        $pk = new RemoveActorPacket();
        $pk->entityUniqueId = $eid;
        $entity->dataPacket($pk);
    }

}