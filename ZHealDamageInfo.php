<?php
/**
 * @name ZHealDamageInfo
 * @author Zaoui267[KR]
 * @main ZHealDamageInfo\ZHealDamageInfo
 * @version Beta0.1
 * @api 3.10.0
 */
namespace HealDamageInfo;

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
        $this->getLogger()->notice("ZHealDamageInfo를 가동합니다. 이 플러그인은 이런 규칙을 지켜주지 않을시 강제로 사용을 중지시킵니다.\n첫째,접속 메세지를 지우지않는다.\n둘째,무단 공유를 하지않아야 하며 공유할시에는 제작자를 밝힌다.");
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
    public function onJoin(PlayerJoinEvent $event)
    {
        $event->getPlayer()->sendMessage("ZHealDamageInfo를 가동합니다. 이 플러그인은 이런 규칙을 지켜주지 않을시 강제로 사용을 중지시킵니다.\n첫째,접속 메세지를 지우지않는다.\n둘째,무단 공유를 하지않아야 하며 공유할시에는 제작자를 밝힌다.");
        $event->getPlayer->addTitle("§aZHealDamageInfo","§b가동");
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