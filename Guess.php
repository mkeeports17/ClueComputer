<?php
class Guess {
    public string $suspect;
    public string $room;
    public string $weapon;
    public int $player;

    // Constructor with parameters
    public function __construct(string $s = "", string $w = "", string $r = "", int $p = 0) {
        $this->suspect = $s;
        $this->weapon = $w;
        $this->room = $r;
        $this->player = $p;
    }

    public function getSuspect() {
        return $this->suspect;
    }

    public function getRoom() {
        return $this->room;
    }

    public function getWeapon() {
        return $this->weapon;
    }

    public function getPlayer() {
        return $this->player;
    }

    // toString equivalent in PHP
    public function __toString(): string {
        return "[P#]" . $this->player . ", [S]" . $this->suspect . ", [W]" . $this->weapon . ", [R]" . $this->room;
    }
}
?>