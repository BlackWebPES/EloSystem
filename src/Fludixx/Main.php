<?php

namespace Fludixx;

use pocketmine\event\Listener;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\level\Location;
use pocketmine\level\particle\BubbleParticle;
use pocketmine\level\particle\ExplodeParticle;
use pocketmine\level\particle\HeartParticle;
use pocketmine\level\particle\HugeExplodeParticle;
use pocketmine\level\Position;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\utils\Terminal;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\PluginTask;
use pocketmine\Player;
use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\utils\Config;
use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\utils\TextFormat as f;
use pocketmine\item\Item;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\entity\Item as ItemEntity;
use pocketmine\math\Vector3;
use pocketmine\math\Vector2;

use pocketmine\level\particle\DustParticle;
use pocketmine\level\particle\FlameParticle;
use pocketmine\level\particle\RedstoneParticle;
use pocketmine\level\particle\LavaParticle;
use pocketmine\level\particle\PortalParticle;

use pocketmine\level\sound\PopSound;
use pocketmine\level\sound\GhastSound;

use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class Main extends PluginBase implements Listener
{

	public $prefix = f::GOLD . "Elo". f::GREEN ."System" . f::GRAY . " | " . f::WHITE;
    public $sucess = f::GREEN;
    public $failure = f::GREEN;
    
    public function onEnable()
	{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info($this->prefix . f::WHITE . f::AQUA . "EloSystem by Fludixx" . f::GREEN .  " wurde Erfolgreich Aktiviert!");
	}
    
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool
    {
        $name = $sender->getName();
        if ($command->getName() == "elo") {
             if((!empty($args['0'])) && $args['0'] == "set"){
                 if(!$sender->hasPermission("elosys.set")) {
                $sender->sendMessage($this->prefix . f::RED . "E: " . f::WHITE . "Verweigert!");
                 return 0;
            }
                 if((!empty($args['1']))){
                     if((!empty($args['2']))){
                        $giveelo = $this->getServer()->getPlayer($args['1']);
                         $giveelon = $giveelo->getName();
                        $elo = new Config("/cloud/elo/".$giveelon.".yml", Config::YAML);
                        $elo->set("elo", (int)$args['2']);
                        $elo->save();
                        $sender->sendMessage($this->prefix . f::GREEN . "Elo Erfolgreich Ersetzt");
                        $giveelo->sendMessage($this->prefix . f::WHITE . "Dein Elo wurde von einem Admin ersetzt! Neuer Wert: " . f::GOLD. $args['2'] );
                         return 1;
                 } else {
                 $sender->sendMessage($this->prefix . f::RED . "E: " . f::WHITE . "Keine Elo Anzahl gefunden!");
                 return 0;
             }
                 } else {
                 $sender->sendMessage($this->prefix . f::RED . "E: " . f::WHITE . "Kein Spilername gegeben!");
                 return 0;
             }
                    
            } else {
                 if((!empty($args['0'])) && $args['0'] == "view"){
                     if(empty($args['1'])) {
                        $view = new Config("/cloud/elo/".$name.".yml", Config::YAML);
                        $amout = $view->get('elo');
                        $sender->sendMessage($this->prefix . f::WHITE . "Dein Elo beträgt: " . f::GOLD . $amout);
                        return 1;
                     } else {
                         $playerc = $this->getServer()->getPlayer($args['1']);
                         $playername = $playerc->getName();
                         $view = new Config("/cloud/elo/".$playername.".yml", Config::YAML);
                         $amout = $view->get('elo');
                         $sender->sendMessage($this->prefix . f::WHITE . "Elo vom Spieler " . f::GOLD . $playername . f::WHITE . " beträgt: " . f::GOLD . $amout);
                         return 1;
                     }
                 }
                 if((!empty($args['0'])) && $args['0'] == "give"){
                     if(!$sender->hasPermission("elosys.set")) {
                $sender->sendMessage($this->prefix . f::RED . "E: " . f::WHITE . "Verweigert!");
                 return 0;
            }
                 if((empty($args['1']))){
                     $sender->sendMessage($this->prefix . f::RED . "E: " . f::WHITE . "Kein Spilername gegeben!");
                     return 0;
                 }
                     if((empty($args['2'])) && !($args['2'] == "elonugget")){
                         $sender->sendMessage($this->prefix . f::RED . "E: " . f::WHITE . "Unbekanntes Item! Items: elonugget");
                         return 0;
                     }
                     $nameid = $this->getServer()->getPlayer($args['1']);
                     $name = $nameid->getName();
                     $inventar = $nameid->getInventory();
                     $nugget = Item::get(371, 0, 1);
	                 $nugget->setCustomName(f::GOLD . "Elo" . f::GREEN . "Nugget");
                     $inventar->setItem(8, $nugget);
                     $nameid->sendMessage($this->prefix . f::GOLD . "Du hast ein " . f::GREEN . "EloNugget " . f::GOLD . "bekommen!");
                     $sender->sendMessage($this->prefix . "Item Verschickt an: " . f::GOLD . $name);
                     return true;
            } else {
                     $sender->sendMessage($this->prefix . f::RED . "E: " . f::WHITE . "Kein Argument gegeben! Args: set, view, give");
                     return 0;
                 }
             }
            
        }
    }
    
    public function onJoin(PlayerJoinEvent $event) {
    $name = $event->getPlayer()->getName();
        @mkdir($this->getDataFolder()); 
        @mkdir($this->getDataFolder() . "elo"); 
     $kconfig = new Config("/cloud/elo/".$name.".yml", Config::YAML);
     if(!$kconfig->get("elo")){
        $kconfig->set("elo", 50);
        $kconfig->save();
     }
    }
    public function onInteract(PlayerInteractEvent $event) {
    	$player = $event->getPlayer();
        $nametag = $player->getName();
        $inventar = $player->getInventory();
        $item = $player->getInventory()->getItemInHand();
        if ($item->getCustomName() == f::GOLD . "Elo" . f::GREEN . "Nugget") {
        	$elo = new Config("/cloud/elo/".$nametag.".yml", Config::YAML);
                        $elo->set("elo", $elo->get("elo")+1000);
                        $elo->save();
                        $player->sendMessage($this->prefix . "[" . f::GREEN . " + 1000 ELO" . f::WHITE ." ]");
                $air = Item::get(0, 0, 0);
            $inventar->setItem(0, $air);
            $inventar->setItem(1, $air);
            $inventar->setItem(2, $air);
            $inventar->setItem(3, $air);
            $inventar->setItem(4, $air);
            $inventar->setItem(5, $air);
            $inventar->setItem(6, $air);
            $inventar->setItem(7, $air);
            $inventar->setItem(8, $air);
                $nugget = Item::get(452, 0, 1);
                $nugget->setCustomName(f::GOLD . "Elo" . f::GREEN . "Nugget" . f::RED . " [EXPIRED]");
                $inventar->setItem(8, $nugget);
            
        }
        
    if ($item->getCustomName() == f::GOLD . "Elo" . f::GREEN . "Nugget" . f::RED . " [EXPIRED]") {
        $player->sendMessage($this->prefix . f::RED . "Das Nugget ist Abgelaufen! :(");
    }
    
}
}