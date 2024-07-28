<?php

namespace KumaDev\ChatCensored;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\event\Listener;

class Main extends PluginBase implements Listener {

    /** @var Config */
    private $wordConfig;

    public function onEnable(): void {
        $this->saveResource("word.yml");
        $this->wordConfig = new Config($this->getDataFolder() . "word.yml", Config::YAML);

        $this->getServer()->getPluginManager()->registerEvents(new ChatListener($this), $this);
    }

    /**
     * Get the list of censored words.
     *
     * @return array
     */
    public function getCensoredWords(): array {
        return $this->wordConfig->get("words", []);
    }

    /**
     * Censor a message if it contains censored words.
     *
     * @param string $message
     * @return string
     */
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
}
