<?php
/**
 * @name ZHealDamageInfo
 * @author Zaoui267[KR]
 * @main ZHealDamageInfo\ZHealDamageInfo
 * @version Beta0.1
 * @api 3.10.0
 */
namespace ZHealDamageInfo;

use pocketmine\plugin\PluginBase;
use pocketmine\event\{
    Listener,
    player\PlayerJoinEvent
};
use pocketmine\event\entity\{
        EntityDamageEvent,
        EntityDamageByEntityEvent as EDB,
        EntityRegainHealthEvent
};
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\Player;
use pocketmine\Server;

class ZHealDamageInfo extends PluginBase implements Listener
{
    public function onEnable()
    {
        Server::getInstance()->getPluginManager()->registerEvents($this,$this);
    }
    public function onDamage(EntityDamageEvent $event)
    {
        if($event instanceof EDB)
        {
            #if($event->getEntity() instanceof Player)
            #{
                $entity = $event->getEntity();
                $damage = $event->getFinalDamage();
                $particle = new FloatingTextParticle(new Vector3($entity->x,$entity->y,$entity->z),'§l§c'.$damage);
                $this->getScheduler()->scheduleDelayedTask(new deleteParticleTask($particle,$entity->level),40);
                $entity->level->addParticle($particle);
            #}
        }
    }
    public function onRegainHealth(EntityRegainHealthEvent $event)
    {
        if($event->getEntity() instanceof Player)
        {
                $entity = $event->getEntity();
                $amount = $event->getAmount();
                $particle = new FloatingTextParticle(new Vector3($entity->x,$entity->y,$entity->z),'§l§a'.$amount);
                $this->getScheduler()->scheduleDelayedTask(new deleteParticleTask($particle,$entity->level),40);
                $entity->level->addParticle($particle);
        }
    }
}

class deleteParticleTask extends Task
{
    private $particle;
    private $level;
    public function __construct($particle,$level)
    {
        $this->particle = $particle;
        $this->level = $level;
    }
    public function onRun($currentTick)
    {
        $this->particle->setInvisible();
        $this->level->addParticle($this->particle);
    }
}
