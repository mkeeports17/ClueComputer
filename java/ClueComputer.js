// Utility functions
const capitalizeWord = (word) => {
    if (!word) return "EmptyWord";
    return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
};

const getIndex = (str, list) => list.indexOf(capitalizeWord(str));

// Guess class
class Guess {
    constructor(suspect = "", weapon = "", room = "", player = 0) {
        this.suspect = suspect;
        this.weapon = weapon;
        this.room = room;
        this.player = player;
    }

    toString() {
        return `[P#]${this.player}, [S]${this.suspect}, [W]${this.weapon}, [R]${this.room}`;
    }
}

// Main ClueComputer logic
class ClueComputer {
    constructor() {
        this.players = 6;
        this.currentPlayer = 1;
        this.guesses = [];
        this.table = [];
        this.currentGuess = new Guess();
        this.suspectList = ["Mustard", "Plum", "Green", "Peacock", "Scarlett", "White"];
        this.weaponList = ["Wrench", "Candlestick", "Dagger", "Revolver", "Leadpipe", "Rope"];
        this.roomList = [
            "Ball", "Billiard", "Conservatory", "Dining", "Hall", "Kitchen", 
            "Library", "Lounge", "Study"
        ];
        this.fullList = [...this.suspectList, ...this.weaponList, ...this.roomList];
    }

    startTable(rows, cols) {
        this.table = Array.from({ length: rows }, (_, i) =>
            Array.from({ length: cols }, () => (i === 1 ? "X" : "-"))
        );
    }

    eliminateHorizontally(row, col) {
        for (let i = 0; i < this.table[row].length; i++) {
            this.table[row][i] = "X";
        }
        this.table[row][col] = "O";
    }

    printTable() {
        console.log("               ", ...Array.from({ length: this.players + 1 }, (_, i) => i));
        const categories = [
            { list: this.suspectList, offset: 0 },
            { list: this.weaponList, offset: this.suspectList.length },
            { list: this.roomList, offset: this.suspectList.length + this.weaponList.length }
        ];
        categories.forEach(({ list, offset }) => {
            list.forEach((item, idx) => {
                console.log(item.padEnd(15), ...this.table[offset + idx]);
            });
            console.log("-".repeat(15), ...Array(this.players + 1).fill("---"));
        });
    }

    async run() {
        const readline = require("readline");
        const rl = readline.createInterface({
            input: process.stdin,
            output: process.stdout,
        });

        const question = (query) =>
            new Promise((resolve) => rl.question(query, resolve));

        console.log("\nWelcome to ClueComputer.");
        this.players = parseInt(await question("Enter number of players: "));
        console.log(
            `\nNumber your opponents 2-${this.players}. Player 0 will be the envelope and player 1 will be you.`
        );
        console.log(
            "I will print out the table at the end of each turn. An X means that player does not have that card, O means that player has that card."
        );
        await question("Hit enter to continue. ");

        this.startTable(this.fullList.length, this.players + 1);
        while (true) {
            const next = await question(
                "\nEnter your next card (or x to continue): "
            );
            if (next === "x") break;
            this.eliminateHorizontally(getIndex(next, this.fullList), 1);
        }
        this.printTable();

        this.currentPlayer = parseInt(await question("\nWhich player took the first turn? (whole number): "));
        this.currentGuess = new Guess();

        while (true) {
            const guess = await question(`\nEnter Player ${this.currentPlayer}'s guess or x to skip: `);
            if (guess === "x") break;

            this.parseGuess(guess);

            const showPlayer = parseInt(await question(
                `Which player showed Player ${this.currentPlayer} a card?(0 if no one did): `
            ));

            this.currentGuess.player = showPlayer;
            if (showPlayer !== 0 && showPlayer !== 1) {
                this.guesses.push(this.currentGuess);
            }

            this.guessElims(showPlayer);

            if (this.currentPlayer === 1 && showPlayer !== 0) {
                const str = await question(`Which card did Player ${showPlayer} show you? `);
                this.eliminateHorizontally(getIndex(str, this.fullList), showPlayer);
            }

            this.checkTable();
            this.printTable();

            this.currentPlayer = this.currentPlayer === this.players ? 1 : this.currentPlayer + 1;
        }

        rl.close();
    }

    parseGuess(guess) {
        const words = guess.split(" ");
        this.currentGuess = new Guess(
            this.parseWord(words[0]),
            this.parseWord(words[1]),
            this.parseWord(words[2]),
            this.currentPlayer
        );
    }

    parseWord(word) {
        word = capitalizeWord(word);
        if (this.suspectList.includes(word)) {
            return word;
        } else if (this.weaponList.includes(word)) {
            return word;
        } else {
            return word;
        }
    }

    guessElims(showPlayer) {
        for (let i = this.currentPlayer + 1; i !== showPlayer; i++) {
            if (i === this.players + 1) i = 0;
            else if (i === this.currentPlayer) break;
            else this.currentGuessElim(i);
        }
    }

    currentGuessElim(col) {
        this.table[getIndex(this.currentGuess.suspect, this.fullList)][col] = "X";
        this.table[getIndex(this.currentGuess.weapon, this.fullList)][col] = "X";
        this.table[getIndex(this.currentGuess.room, this.fullList)][col] = "X";
    }

    checkTable() {
        for (let i = 0; i < this.table.length; i++) {
            const allX = this.table[i].slice(1).every((cell) => cell === "X");
            if (allX) this.table[i][0] = "O";
        }
    }
}

// Run the program
const game = new ClueComputer();
game.run();
