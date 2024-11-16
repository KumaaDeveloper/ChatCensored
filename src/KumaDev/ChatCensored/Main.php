<?php

namespace KumaDev\ChatCensored;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\player\Player;
use pocketmine\Server;

class Main extends PluginBase implements Listener {

    /** @var Config */
    private $config;

    public function onEnable(): void {
        $this->saveResource("config.yml");
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function getCensoredWords(): array {
        return $this->config->get("words", []);
    }

    public function isOpAllowed(Player $player): bool {
        return $this->config->get("allow-op", false) && Server::getInstance()->isOp($player->getName());
    }

    public function censorMessage(string $message): string {
        $words = $this->getCensoredWords();
        foreach ($words as $word) {
            $pattern = '/' . preg_quote($word, '/') . '/i';
            $message = preg_replace_callback($pattern, function($matches) {
                $censored = substr($matches[0], 0, 1) . str_repeat('*', strlen($matches[0]) - 1);
                return $censored;
            }, $message);
        }
        return $message;
    }

    public function onPlayerChat(PlayerChatEvent $event): void {
        $player = $event->getPlayer();
        if ($this->isOpAllowed($player)) {
            return;
        }

        $message = $event->getMessage();
        $censoredMessage = $this->censorMessage($message);
        $event->setMessage($censoredMessage);
    }
}