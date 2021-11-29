<?php

namespace SharkMCPE\operator;

use pocketmine\Player;

class region
{
    public function inArea(Player $player,$minX,$minZ,$maxX,$maxZ) : bool {
        //PlayerPos
        $x = $player->x;
        $z = $player->z;
        //minPos
        $x_min = $minX;
        $x_max = $maxX;
        //maxPos
        $z_min = $minZ;
        $z_max = $maxZ;
        if (!($x >= $x_min and $x <= $x_max and $z >= $z_min and $z <= $z_max)){//ตรงนี้จะเปลี่ยนก็ได้นะครับเป็นช่วงของจำนวนจริงโดยที่ variable ใดๆ เป็นสมาชิกของ R
            return false;
        }
        return true;
    }
}