<?php

namespace Ticket;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecuter;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\Server;
use onebone\economyapi\EconomyAPI;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class main extends PluginBase implements Listener{

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getLogger()->info("Ticketを読み込みました");
		if(!file_exists($this->getDataFolder())){
			mkdir($this->getDataFolder(), 0744, true);
		}
		$this->height1 = new Config($this->getDataFolder() ."height1.yml", Config::YAML);
		$this->height2 = new Config($this->getDataFolder() ."height2.yml", Config::YAML);
		$this->height3 = new Config($this->getDataFolder() ."height3.yml", Config::YAML);
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		switch($command->getName()){
			case "ticket":
			$economy = EconomyAPI::getInstance();
			$PlayerHasMoney = EconomyAPI::getInstance()->myMoney($sender->getName());
			if($sender instanceof Player){
			    if(!isset($args[0], $args[1])){
			        $sender->sendMessage("§e〜許可証一覧〜");
			        $sender->sendMessage("許可証名 : 価格 : 内容");
			        $sender->sendMessage("height1 : 1000 : 高さ70以上80未満許可証");
			        $sender->sendMessage("height2 : 2000 : 高さ80以上90未満許可証");
			        $sender->sendMessage("height3 : 3000 : 高さ90以上100未満許可証");
			        $sender->sendMessage("use: /ticket <許可証名> <土地番号>");
			        }else{
			            switch($args[0]){
			                case "height1":
			                $name = $sender->getName();
			                if($this->height1->exists($args[1])){
			                    $sender->sendMessage("§e>>土地番号 {$args[1]} のheight1は既に購入済みです");
			                }else{
			                    if($PlayerHasMoney >= 1000){
			                        $this->height1->set($args[1],$name);
			                        $this->height1->save();
			                        $this->height1->reload();
			                        $economy->reduceMoney($name, 1000);
			                        $sender->sendMessage("§e>>土地番号 {$args[1]} のheight1を1000円で購入しました");
			                    }else{
			                        $sender->sendMessage("§e>>所持金が不足しています");
			                    }
			                }
			                return true;
			                
			                case "height2":
			                $name = $sender->getName();
			                if($this->height1->exists($args[1])){
			                    if($this->height2->exists($args[1])){
			                        $sender->sendMessage("§e>>土地番号 {$args[1]} のheight2は既に購入済みです");
			                    }else{
			                        if($PlayerHasMoney >= 2000){
			                            $this->height2->set($args[1],$name);
			                            $this->height2->save();
			                            $this->height2->reload();
			                            $economy->reduceMoney($name, 2000);
			                            $sender->sendMessage("§e>>土地番号 {$args[1]} のheight2を2000円で購入しました");
			                        }else{
			                            $sender->sendMessage("§e>>所持金が不足しています");
			                        }
			                    }
			                }else{
			                    $sender->sendMessage("§e>>土地番号 {$args[1]} のheight1が未購入です先に購入してください");
			                }
			                return true;
			                
			                case "height3":
			                $name = $sender->getName();
			                if($this->height2->exists($args[1])){
			                    if($this->height3->exists($args[1])){
			                        $sender->sendMessage("§e>>土地番号 {$args[1]} のheight3は既に購入済みです");
			                    }else{
			                        if($PlayerHasMoney >= 3000){
			                            $this->height3->set($args[1],$name);
			                            $this->height3->save();
			                            $this->height3->reload();
			                            $economy->reduceMoney($name, 3000);
			                            $sender->sendMessage("§e>>土地番号 {$args[1]} のheight3を3000円で購入しました");
			                        }else{
			                            $sender->sendMessage("§e>>所持金が不足しています");
			                        }
			                    }
			                }else{
			                    $sender->sendMessage("§e>>土地番号 {$args[1]} のheight2が未購入です先に購入してください");
			                }
			                return true;
			                
			                default:
			                    $sender->sendMessage("use: /ticket <許可証名> <土地番号>");
			                return true;
			            }
			        }
			    }
            return true;
			    
			case "delticket":
			$economy = EconomyAPI::getInstance();
			$PlayerHasMoney = EconomyAPI::getInstance()->myMoney($sender->getName());
			if($sender instanceof Player && $sender->isOp()){
			    if(!isset($args[0], $args[1], $args[2])){
			        $sender->sendMessage("§e〜許可証一覧〜 (売却価格)");
			        $sender->sendMessage("許可証名 : 売却価格 : 内容");
			        $sender->sendMessage("height1 : 100 : 高さ70以上80未満許可証");
			        $sender->sendMessage("height2 : 200 : 高さ80以上90未満許可証");
			        $sender->sendMessage("height3 : 300 : 高さ90以上100未満許可証");
			        $sender->sendMessage("use: /delticket <許可証名> <土地番号> <名前>");
			        $sender->sendMessage("§l§e注意 §d1番高さが高い順から売却してください");
			        }else{
			            switch($args[0]){
			                case "height1":
			                $name = $sender->getName();
			                $player = $this->getServer()->getPlayer($args[2]);//オブジェクト取得
			                if(!$player == NULL){
			                    if($this->height1->exists($args[1])){
			                        $this->height1->remove($args[1]);
			                        $this->height1->save();
			                        $this->height1->reload();
			                        $economy->addMoney($player->getName(),100);
			                        $sender->sendMessage("§e>>土地番号 {$args[1]} のheight1を売却しました");
			                        $player->sendMessage("§e>>{$sender->getName()}に土地番号 {$args[1]} のheight1を売却されました");
			                    }else{
			                        $sender->sendMessage("§e>>土地番号 {$args[1]} のheight1は未購入です");
			                    }
			                }else{
			                    $sender->sendMessage("§e>>{$args[2]}はオフラインです");
			                }
			                return true;
			                
			                case "height2":
			                $name = $sender->getName();
			                $player = $this->getServer()->getPlayer($args[2]);
			                if(!$player == NULL){
			                    if($this->height2->exists($args[1])){
			                        $this->height2->remove($args[1]);
			                        $this->height2->save();
			                        $this->height2->reload();
			                        $economy->addMoney($player->getName(),200);
			                        $sender->sendMessage("§e>>土地番号 {$args[1]} のheight2を売却しました");
			                        $player->sendMessage("§e>>{$sender->getName()}に土地番号 {$args[1]} のheight2を売却されました");
			                    }else{
			                        $sender->sendMessage("§e>>土地番号 {$args[1]} のheight2は未購入です");
			                    }
			                }else{
			                    $sender->sendMessage("§e>>{$args[2]}はオフラインです");
			                }
			                return true;
			                
			                case "height3":
			                $name = $sender->getName();
			                $player = $this->getServer()->getPlayer($args[2]);
			                if(!$player == NULL){
			                    if($this->height3->exists($args[1])){
			                        $this->height3->remove($args[1]);
			                        $this->height3->save();
			                        $this->height3->reload();
			                        $economy->addMoney($player->getName(),300);
			                        $sender->sendMessage("§e>>土地番号 {$args[1]} のheight3を売却しました");
			                        $player->sendMessage("§e>>{$sender->getName()}に土地番号 {$args[1]} のheight3を売却されました");
			                    }else{
			                        $sender->sendMessage("§e>>土地番号 {$args[1]} のheight3は未購入です");
			                    }
			                }else{
			                    $sender->sendMessage("§e>>{$args[2]}はオフラインです");
			                }
			                return true;
			                
			                default:
			                    $sender->sendMessage("use: /delticket <許可証名> <土地番号> <名前>");
			                return true;
			            }
			        }
			    }
            return true;

			case "tickets":
			if($sender instanceof Player){
			    if(!isset($args[0])){
			       $sender->sendMessage("use: /tickets <許可証名> or /tickets check"); 
			        }else{
			            switch($args[0]){
			                 case "height1":
			                 $sender->sendMessage("§eheight1 購入済み土地番号");
			                 foreach($this->height1->getAll() as $key=>$value){//height1にある全ての$keyを取得			                    
			                     $sender->sendMessage("土地番号 : {$key} (所有者 {$value})");//購入者の名前を表示 ($keyから)
			                 }
			                 return true;
			                 
			                 case "height2":
			                 $sender->sendMessage("§eheight2 購入済み土地番号");
			                 foreach($this->height2->getAll() as $key=>$value){
			                     $sender->sendMessage("土地番号 : {$key} (所有者 {$value})");
			                 }
			                 return true;
			                 
			                 case "height3":
			                 $sender->sendMessage("§eheight3 購入済み土地番号");
			                 foreach($this->height3->getAll() as $key=>$value){
			                     $sender->sendMessage("土地番号 : {$key} (所有者 {$value})");
			                 }
			                 return true;
			                 
			                 case "check":
			                 if(!isset($args[1])){
			                     $sender->sendMessage("use: /tickets check <土地番号>");
			                 }else{
			                     if($this->height1->exists($args[1]) && !$this->height2->exists($args[1]) && !$this->height3->exists($args[1])){
			                         $sender->sendMessage("§e>>土地番号 {$args[1]} はheight1を購入済みです");
			                     }
			                     if($this->height2->exists($args[1]) && !$this->height3->exists($args[1])){
			                         $sender->sendMessage("§e>>土地番号 {$args[1]} はheight2を購入済みです");
			                     }
			                     if($this->height3->exists($args[1])){
			                         $sender->sendMessage("§e>>土地番号 {$args[1]} はheight3を購入済みです");
			                     }
			                     if(!$this->height1->exists($args[1]) && !$this->height2->exists($args[1]) && !$this->height3->exists($args[1])){
			                         $sender->sendMessage("§e>>土地番号 {$args[1]} の許可証が見つかりませんでした");
			                     }
			                 }
			                 return true;
			                 
			                 default:
			                     $sender->sendMessage("use: /tickets <許可証名> or /tickets check");
			                 return true;
			            }
			    }
			}
			return true;
		}
	}
}
