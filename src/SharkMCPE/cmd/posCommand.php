<?php

namespace SharkMCPE\cmd;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use SharkMCPE\db\yaml;
use SharkMCPE\Guncha;

class posCommand extends Command implements PluginIdentifiableCommand
{

    private $plugin;

    public function __construct(Guncha $plugin)
    {
        parent::__construct("p1", "save p1");
        $this->plugin = $plugin;
        $this->setPermission("pos.cmd");
    }

    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, $commandLabel, array $args)
    {
        if($sender instanceof Player){
            if(empty($args)){
                $yaml = new yaml($this->plugin);
                $player = $sender->getPlayer();
                $minX = $player->getFloorX();
                $minY = $player->getFloorY();
                $minZ = $player->getFloorZ();
                $yaml->setPos1($minX,$minY,$minZ);
                $player->sendMessage("Success Pos1. Next Pos2");
                return true;
            }
        }
    }
}