<?php

namespace SharkMCPE\task;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\level\particle\HappyVillagerParticle;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use SharkMCPE\db\player_yaml;
use SharkMCPE\db\yaml;
use SharkMCPE\Guncha;
use SharkMCPE\operator\region;
use SharkMCPE\particle\Particle;

class InArea extends Task
{

    private $plugin;

    public function __construct(Guncha $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onRun(int $currentTick)
    {
        $lvl = Server::getInstance()->getLevels();
        $yaml = new yaml($this->plugin);
        $player_db = new player_yaml($this->plugin);
        $particle = new Particle($this->plugin);
        $array = [];
        if ($yaml->getCountRegion() !== 0) { //Count Region (int)
            foreach ($yaml->getRegion() as $regions) {
                $array[] = $regions;
            }
        }
        foreach ($array as $region_name) {
            $x_pos1 = $yaml->getPos($region_name, "Pos1", "x");
            $z_pos1 = $yaml->getPos($region_name, "Pos1", "z");
            $x_pos2 = $yaml->getPos($region_name, "Pos2", "x");
            $z_pos2 = $yaml->getPos($region_name, "Pos2", "z");
            if ($x_pos1 > $x_pos2) {
                $minX = $x_pos2;
                $maxX = $x_pos1;
            } else {
                $minX = $x_pos1;
                $maxX = $x_pos2;
            }
            if ($z_pos1 > $z_pos2) {
                $minZ = $z_pos2;
                $maxZ = $z_pos1;
            } else {
                $minZ = $z_pos1;
                $maxZ = $z_pos2;
            }
            //echo $minX." minZ ".$minZ." maxX ".$maxX." maxZ ".$maxZ;
            foreach ($lvl as $levels) {
                $entities = $levels->getEntities(); //GET entity ในทุก level
                $region = new region();
                foreach ($entities as $entity) {
                    if ($entity instanceof Player) {
                        $player = $entity;
                        if ($region->inArea($player, $minX, $minZ, $maxX, $maxZ)) {
                            if ($player->isSneaking()) {
                                $player_db->minusTime($player);
                                $player->sendPopup("§cกำ§aลั§eง§dเ§5ก็§6บ§l§2กัญ§aชา §6".$player_db->getTime($player)." §cวิ");
                                if ($player_db->getTime($player) <= 0) {
                                    $level = $player->getLevel();
                                    $x = $player->x;
                                    $y = $player->y;
                                    $z = $player->z;
                                    $level->addParticle(new HappyVillagerParticle(new Vector3($x, $y + 2.5, $z)));
                                    $level->addParticle(new HappyVillagerParticle(new Vector3($x, $y+1, $z+1)));
                                    $level->addParticle(new HappyVillagerParticle(new Vector3($x, $y+1, $z-1)));
                                    $level->addParticle(new HappyVillagerParticle(new Vector3($x+1, $y+1, $z)));
                                    $level->addParticle(new HappyVillagerParticle(new Vector3($x-1, $y+1, $z)));
                                    $weed = Item::get($yaml->getSettingTwofac("item","id"),
                                        $yaml->getSettingTwofac("item","meta"),
                                        $yaml->getSettingTwofac("item","amount"));
                                    $weed->setCustomName($yaml->getSettingTwofac("item","item_name"));
                                    $player->getInventory()->addItem($weed);
                                    $amount = $yaml->getSettingTwofac("item","amount");
                                    $particle->LootedParticle($player,$amount);
                                    $player->sendMessage("§f+ §l§2กัญ§aชา §6* §c".$amount);
                                    $player_db->setTime($player, $yaml->getTime());
                                } else {
                                    $level = $player->getLevel();
                                    $level->addParticle(new DestroyBlockParticle($player->add(mt_rand(-50, 50) / 100,
                                        1 + mt_rand(-50, 50) / 100,
                                        mt_rand(-50, 50) / 100),
                                        Block::get(Block::EMERALD_BLOCK))); //ตรงนี้อยากเปลี่ยนก็คำนวณเองเลยนะครับ แนะนำใช้กฏ cosine จะง่ายกว่า
                                }
                            } else {
                                $player->sendPopup($yaml->getMsg());
                                $player_db->setTime($player, $yaml->getTime());
                            }
                        } else {
                            //$player_db->setTime($player, $yaml->getTime());
                        }
                    }
                }
            }
        }
    }
}