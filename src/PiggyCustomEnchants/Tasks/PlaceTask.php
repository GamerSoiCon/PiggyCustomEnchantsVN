<?php

namespace PiggyCustomEnchants\Tasks;

use PiggyCustomEnchants\Main;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;

class PlaceTask extends PluginTask
{
    private $plugin;
    private $position;
    private $level;
    private $seed;
    private $player;

    public function __construct(Main $plugin, Vector3 $position, Level $level, Item $seed, Player $player)
    {
        $this->plugin = $plugin;
        $this->position = $position;
        $this->level = $level;
        $this->seed = $seed;
        $this->player = $player;
        parent::__construct($plugin);
    }

    public function onRun(int $currentTick)
    {
        $this->level->useItemOn($this->position, $this->seed, 1, $this->position, $this->player);
        $this->player->getInventory()->removeItem(Item::get($this->seed->getId(), 0, 1));
    }
}
