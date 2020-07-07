<?php

namespace PiggyCustomEnchants\Tasks;

use PiggyCustomEnchants\CustomEnchants\CustomEnchantsIds;
use PiggyCustomEnchants\Main;
use pocketmine\entity\object\ItemEntity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\level\particle\FlameParticle;
use pocketmine\math\Vector3;
use pocketmine\scheduler\PluginTask;

class ForcefieldTask extends PluginTask
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
            $forcefields = 0;
            foreach ($player->getArmorInventory()->getContents() as $armor) {
                $enchantment = $armor->getEnchantment(CustomEnchantsIds::FORCEFIELD);
                if ($enchantment !== null) {
                    $forcefields += $enchantment->getLevel();
                }
            }
            if ($forcefields > 0) {
                $radius = $forcefields * 0.75;
                $entities = $player->getLevel()->getNearbyEntities($player->getBoundingBox()->grow($radius, $radius, $radius), $player);
                foreach ($entities as $entity) {
                    if ($entity instanceof Projectile) {
                        if ($entity->getOwningEntity() !== $player) {
                            $entity->setMotion($entity->getMotion()->multiply(-1));
                        }
                    } else {
                        if (!$entity instanceof ItemEntity && is_null($entity->namedtag->getString("SlapperVersion"))) {
                            $entity->setMotion(new Vector3($player->subtract($entity)->normalize()->multiply(-0.75)->x, 0, $player->subtract($entity)->normalize()->multiply(-0.75)->z));
                        }
                    }
                }
                if (!isset($this->plugin->forcefieldParticleTick[$player->getLowerCaseName()])) {
                    $this->plugin->forcefieldParticleTick[$player->getLowerCaseName()] = 0;
                }
                $this->plugin->forcefieldParticleTick[$player->getLowerCaseName()]++;
                if ($this->plugin->forcefieldParticleTick[$player->getLowerCaseName()] >= 7.5) {
                    $diff = $radius / $forcefields;
                    for ($theta = 0; $theta <= 360; $theta += $diff) {
                        $x = $radius * sin($theta);
                        $y = 0.5;
                        $z = $radius * cos($theta);
                        $pos = $player->add($x, $y, $z);
                        $player->getLevel()->addParticle(new FlameParticle($pos));
                    }
                    $this->plugin->forcefieldParticleTick[$player->getLowerCaseName()] = 0;
                }
            }
        }
    }
}
