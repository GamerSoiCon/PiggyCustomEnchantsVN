<?php

namespace PiggyCustomEnchants\Tasks;

use PiggyCustomEnchants\CustomEnchants\CustomEnchantsIds;
use PiggyCustomEnchants\Main;
use pocketmine\network\mcpe\protocol\SetSpawnPositionPacket;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;

class RadarTask extends PluginTask
{
    private $plugin;
    private $radars;

    public function __construct(Main $plugin)
    {
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }

    public function onRun(int $currentTick)
    {
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            $radar = false;
            foreach ($player->getInventory()->getContents() as $item) {
                $enchantment = $item->getEnchantment(CustomEnchantsIds::RADAR);
                if ($enchantment !== null) {
                    $detected = $this->plugin->findNearestEntity($player, $enchantment->getLevel() * 50, Player::class, $player);
                    if (!is_null($detected)) {
                        $pk = new SetSpawnPositionPacket();
                        $pk->x = (int)$detected->x;
                        $pk->y = (int)$detected->y;
                        $pk->z = (int)$detected->z;
                        $pk->spawnForced = true;
                        $pk->spawnType = SetSpawnPositionPacket::TYPE_WORLD_SPAWN;
                        $player->dataPacket($pk);
                        $radar = true;
                        $this->radars[$player->getLowerCaseName()] = true;
                        if ($item->equalsExact($player->getInventory()->getItemInHand())) {
                            $player->sendTip(TextFormat::GREEN . "Người chơi ở gần còn " . round($player->distance($detected), 1) . " blocks");
                        }
                        break;
                    } else {
                        if ($item->equalsExact($player->getInventory()->getItemInHand())) {
                            $player->sendTip(TextFormat::RED . "Không tìm thấy người chơi !");
                        }
                    }
                }
            }
            if (!$radar) {
                if (isset($this->radars[$player->getLowerCaseName()])) {
                    $pk = new SetSpawnPositionPacket();
                    $pk->x = (int)$player->getLevel()->getSafeSpawn()->x;
                    $pk->y = (int)$player->getLevel()->getSafeSpawn()->y;
                    $pk->z = (int)$player->getLevel()->getSafeSpawn()->z;
                    $pk->spawnForced = true;
                    $pk->spawnType = SetSpawnPositionPacket::TYPE_WORLD_SPAWN;
                    $player->dataPacket($pk);
                    unset($this->radars[$player->getLowerCaseName()]);
                }
            }
        }
    }
}
