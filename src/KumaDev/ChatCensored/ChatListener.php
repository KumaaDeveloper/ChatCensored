<?php

namespace KumaDev\ChatCensored;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;

class ChatListener implements Listener {

    /** @var Main */
    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @param PlayerChatEvent $event
     */
    public function onPlayerChat(PlayerChatEvent $event): void {
        $message = $event->getMessage();
        $censoredMessage = $this->plugin->censorMessage($message);
        $event->setMessage($censoredMessage);
    }
}
