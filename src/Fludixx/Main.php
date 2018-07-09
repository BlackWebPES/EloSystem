<?php

namespace Fludixx;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\level\Location;
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
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->getLogger()->info($this->prefix . f::WHITE . f::AQUA . "EloSystem by Fludixx" . f::GREEN .  " wurde Erfolgreich Aktiviert!");
	}
    //ORDINAL SUFFIX FOR RANKING
    function ordinal($number) {
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13))
        return $number. 'th';
    else
        return $number. $ends[$number % 10];
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
                 if((!empty($args['0'])) && $args['0'] == "giftnugget"){
                     if(!$sender->hasPermission("elosys.set")) {
                $sender->sendMessage($this->prefix . f::RED . "E: " . f::WHITE . "Verweigert!");
                 return 0;
            }
                 if((empty($args['1']))){
                     $sender->sendMessage($this->prefix . f::RED . "E: " . f::WHITE . "Kein Spielername gegeben!");
                     return 0;
                 }
                     $nameid = $this->getServer()->getPlayer($args['1']);
                     $name = $nameid->getName();
                     $elo = new Config("/cloud/elo/".$name.".yml", Config::YAML);
                     $amout = $elo->get("nugget");
                     $elo->set("nugget", $amout+1);
                     $elo->save();
                     $sender->sendMessage($this->prefix . "Item Verschickt an: " . f::GOLD . $name);
                     $nameid->sendMessage(f::GOLD . $sender->getName() . f::WHITE . " hat dir ein Nugget überwiesen!");
                     $nameid->sendMessage(f::WHITE . "Hole es mit " . f::GREEN . "/elo nugget" . f::WHITE . "ab!");
                     return true;
            }
                 if((!empty($args['0'])) && $args['0'] == "info"){
                     $sender->sendMessage(f::WHITE . "Author: " . f::GREEN . "Fludixx");
                     $sender->sendMessage(f::WHITE . "Name  : " . f::GREEN . "EloSystem");
            }
                 if((!empty($args['0'])) && $args['0'] == "nugget"){
                     $name = $sender->getName();
                     $elo = new Config("/cloud/elo/".$name.".yml", Config::YAML);
                    $amout = $elo->get("nugget");
                    if(!($amout > 0 )) {
                        $sender->sendMessage($this->prefix . f::RED . "Du hast keine Nuggets! :(");
                        return 1;
                    }
                    $elo->set("nugget", $amout-1);
                    $elo->save();
                    $inventar = $sender->getInventory();
                    $nugget = Item::get(371, 0, 1);
	                $nugget->setCustomName(f::GOLD . "Elo" . f::GREEN . "Nugget");
                    $inventar->setItem(8, $nugget);
                    $sender->sendMessage($this->prefix . f::GOLD . "Du hast ein " . f::GREEN . "EloNugget " . f::GOLD . "bekommen!");
                     return true;
            } 
                 if((!empty($args['0'])) && $args['0'] == "ranking"){
                     $name = $sender->getName();
                     //RELOADEN DES EIGENEN EINTRAGES
                     $elo = new Config("/cloud/elo/" . $name . ".yml", Config::YAML);
                     $currentelo = $elo->get("elo");
                     $ranking = new Config("/cloud/elo/ranking.yml", Config::YAML);
                     $ranking->set($name, $currentelo);
                     $ranking->save();
                     // AUFLISTEN
                     $ranking = new Config("/cloud/elo/ranking.yml", Config::YAML);
                     $rankingarray = $ranking->getAll();
                     arsort($rankingarray);
                     $rankingordnung = array_keys($rankingarray);
                     $raningname = array_values($rankingarray);
                     $sender->sendMessage(f::WHITE . ">----" . f::GREEN . "Top 3". f::WHITE . "----<");
                     for($i = 0; $i < 3; $i++){
                        $sender->sendMessage(f::YELLOW . $this->ordinal($i+1) . f::WHITE ." > ". f::GOLD . $rankingordnung[$i].": ". f::GREEN . $raningname[$i]." Elo");
                    }
                     return true;
            } else {
                     $sender->sendMessage($this->prefix . f::RED . "E: " . f::WHITE . "Kein Argument gegeben! Args: set, view, giftnugget, nugget, ranking");
                     return 0;
                 }
             }
            
        }
        if ($command->getName() == "ranking") {
            $name = $sender->getName();
                     //RELOADEN DES EIGENEN EINTRAGES
                     $elo = new Config("/cloud/elo/" . $name . ".yml", Config::YAML);
                     $currentelo = $elo->get("elo");
                     $ranking = new Config("/cloud/elo/ranking.yml", Config::YAML);
                     $ranking->set($name, $currentelo);
                     $ranking->save();
                     // AUFLISTEN
                     $ranking = new Config("/cloud/elo/ranking.yml", Config::YAML);
                     $rankingarray = $ranking->getAll();
                     arsort($rankingarray);
                     $rankingordnung = array_keys($rankingarray);
                     $raningname = array_values($rankingarray);
                     $sender->sendMessage(f::WHITE . ">----" . f::GREEN . "Top 3". f::WHITE . "----<");
                     for($i = 0; $i < 3; $i++){
                        $sender->sendMessage(f::YELLOW . $this->ordinal($i+1) . f::WHITE ." > ". f::GOLD . $rankingordnung[$i].": ". f::GREEN . $raningname[$i]." Elo");
        }
            return true;
        }
        
        
    }
    
    public function onJoin(PlayerJoinEvent $event) {
        $name = $event->getPlayer()->getName();
        @mkdir($this->getDataFolder()); 
        @mkdir($this->getDataFolder() . "elo"); 
     $kconfig = new Config("/cloud/elo/".$name.".yml", Config::YAML);
     if(!$kconfig->get("elo") && !$kconfig->get("nugget")){
        $kconfig->set("elo", 50);
        $kconfig->set("nugget", 0);
        $kconfig->save();
     }
        // Anmeldung für Ranking
        $ranking = new Config("/cloud/elo/ranking.yml", Config::YAML);
        $currentelo = $kconfig->get("elo");
        $ranking->set($name, $currentelo);
        $ranking->save();
        $event->getPlayer()->sendMessage($this->prefix . "Du wurdest atom. in die Ranking Tabelle Eingetragen!");
    $amout = $kconfig->get("elo");
    $event->setJoinMessage(f::WHITE . "[" . f::GREEN . " + $name " . f::WHITE . "(" . f::GOLD . $amout . f::WHITE . ") ]");
    }
    public function onInteract(PlayerInteractEvent $event) {
    	$player = $event->getPlayer();
        $nametag = $player->getName();
        $inventar = $player->getInventory();
        $item = $player->getInventory()->getItemInHand();
        if ($item->getCustomName() == f::GOLD . "Elo" . f::GREEN . "Nugget") {
        	$elo = new Config("/cloud/elo/".$nametag.".yml", Config::YAML);
                        $elo->set("elo", $elo->get("elo")+100);
                        $elo->save();
                        $player->sendMessage($this->prefix . "[" . f::GREEN . " + 100 ELO" . f::WHITE ." ]");
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
    
    public function onDeath(PlayerDeathEvent $event){
        $loser = $event->getPlayer();
        $losername = $loser->getName();
        $cause = $loser->getLastDamageCause();
        if($cause instanceof EntityDamageByEntityEvent and $cause->getDamager() instanceof Player){
        $winner = $cause->getDamager();
        $winnername = $winner->getName();
            //ELO PROCESSOR
            $diff = mt_rand(1, 15); // Random Elo Between 1 and 15
            // WINNER
            $elo = new Config("/cloud/elo/".$winnername.".yml", Config::YAML);
            $amout2 = $elo->get("elo");
            $elo->set("elo", (int)$amout2+(int)$diff);
            $elo->save();
            $result = (int)$amout2+(int)$diff;
            $winner->sendMessage(f::WHITE . "Du hast " . f::GOLD . $losername . f::WHITE . " getötet! " . f::GREEN . "+" . $diff . f::WHITE . " (" . f::GOLD . $result . f::WHITE . ")");
            //LOSER
            $elo = new Config("/cloud/elo/".$losername.".yml", Config::YAML);
            $amout = $elo->get("elo");
            $elo->set("elo", (int)$amout-(int)$diff);
            $elo->save();
            $result = (int)$amout-(int)$diff;
            $loser->sendMessage(f::WHITE . "Du wurdest von " . f::GOLD . $winnername . f::WHITE . " getötet! " . f::RED . "-" . $diff . f::WHITE . " (" . f::GOLD . $result . f::WHITE . ")");
            //DEATH MSG
            $event->setDeathMessage(f::GOLD . $losername . f::WHITE . " (" . f::RED . $amout . f::WHITE . ") wurde von " . f::GOLD . $winnername . f::WHITE . " (" . f::GREEN . $amout2 . f::WHITE . ") getötet!" );
            //ELONUGGET
            $warscheinlichkeit = mt_rand(1, 100);
            if($warscheinlichkeit == 54) {
                $winner->sendMessage(f::GOLD . "Herzlichen Glückwunsch!");
                $winner->sendMessage(f::GOLD . "Du hast ein " . f::GREEN . "EloNugget" . f::GOLD . "gewonnen!");
                $winner->sendMessage(f::WHITE . "Hole es mit " . f::GREEN . "/elo nugget" . f::WHITE . "ab!");
                $elo = new Config("/cloud/elo/".$winnername.".yml", Config::YAML);
                $amout = $elo->get("nugget");
                $elo->set("nugget", $amout+1);
                $elo->save();
                }
            }
        }
    }
