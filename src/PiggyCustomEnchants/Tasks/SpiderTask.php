<?php

namespace PiggyCustomEnchants\Tasks;

use PiggyCustomEnchants\CustomEnchants\CustomEnchantsIds;
use PiggyCustomEnchants\Main;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\scheduler\PluginTask;

class SpiderTask extends PluginTask
{
    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct($plugin);
    }

    public function onRun(int $currentTick)
    {
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            $enchantment = $player->getArmorInventory()->getChestplate()->getEnchantment(CustomEnchantsIds::SPIDER);
            if ($enchantment !== null) {
                $blocks = array_merge($player->getLevel()->getBlock($player)->getHorizontalSides(), $player->getLevel()->getBlock($player->add(0, 1))->getHorizontalSides());
                $solid = 0;
                foreach ($blocks as $block) {
                    if ($block->getId() !== Block::AIR && $block->isSolid()) {
                        $solid++;
                    }
                }
                if ($solid > 0) {
                    if (!$player->getGenericFlag(Entity::DATA_FLAG_WALLCLIMBING)) {
                        $player->setGenericFlag(Entity::DATA_FLAG_WALLCLIMBING, true);
                    }
                    $player->resetFallDistance();
                } else {
                    if ($player->getGenericFlag(Entity::DATA_FLAG_WALLCLIMBING)) {
                        $player->setGenericFlag(Entity::DATA_FLAG_WALLCLIMBING, false);
                    }
                }
            } else {
                if ($player->getGenericFlag(Entity::DATA_FLAG_WALLCLIMBING)) {
                    $player->setGenericFlag(Entity::DATA_FLAG_WALLCLIMBING, false);
                }
            }
        }
    }
}
