class Guess {
    /**
     * Constructor for the Guess class.
     * @param {string} s - The suspect.
     * @param {string} w - The weapon.
     * @param {string} r - The room.
     * @param {number} p - The player number.
     */
    constructor(s = "", w = "", r = "", p = 0) {
        this.suspect = s;
        this.weapon = w;
        this.room = r;
        this.player = p;
    }

    getSuspect() {
        return this.suspect;
    }

    getRoom() {
        return this.room;
    }

    getWeapon() {
        return this.weapon;
    }

    getPlayer() {
        return this.player;
    }

    /**
     * Returns a string representation of the Guess object.
     * @returns {string}
     */
    toString() {
        return `[P#]${this.player}, [S]${this.suspect}, [W]${this.weapon}, [R]${this.room}`;
    }
}