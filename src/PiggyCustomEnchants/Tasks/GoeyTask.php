<?php

namespace PiggyCustomEnchants\Tasks;

use PiggyCustomEnchants\Main;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\scheduler\PluginTask;

class GoeyTask extends PluginTask
{
    private $plugin;
    private $entity;
    private $level;

    public function __construct(Main $plugin, Entity $entity, $level)
    {
        parent::__construct($plugin);
        $this->plugin = $plugin;
        $this->entity = $entity;
        $this->level = $level;
    }

    public function onRun(int $currentTick)
    {
        $this->entity->setMotion(new Vector3($this->entity->getMotion()->x, (3 * $this->level * 0.05) + 0.75, $this->entity->getMotion()->z));
    }
}
