<?php

namespace SharkMCPE\cmd;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use SharkMCPE\db\yaml;
use SharkMCPE\Guncha;

class excCommand extends Command implements PluginIdentifiableCommand
{

    private $plugin;

    public function __construct(Guncha $plugin)
    {
        parent::__construct("exc", "Confirm Your Region");
        $this->plugin = $plugin;
        $this->setPermission("exc.cmd");
    }

    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, $commandLabel, array $args)
    {
        if($sender instanceof Player) {
            if (!isset($args[0])) {
                $sender->sendMessage("Pls /exc (ชื่อพื้นที่จ้าาา)");
                return true;
            }
            $yaml = new yaml($this->plugin);
            $player = $sender->getPlayer();
            $data = $yaml->getDatabase()->getAll();
            $name = $args[0];
            if (!isset($data["Region"][$name])) {
                $sender->sendMessage("§f[§bR§ce§ag§bi§eo§6n§f] §cไม่พบพื้นที่ " . $name);
                return true;
            }
            if ($yaml->getPos1() == "null" || $yaml->getPos2() == "null") {
                $player->sendMessage("Pls /p1 or /p2 first");
                return true;
            }
            $world = $sender->getLevel()->getName();
            $yaml->executeData($name,$yaml->getPos1(),$yaml->getPos2(),$world);
            $player->sendMessage("Success Execute");
            $yaml->resetPos();
        }
    }

}