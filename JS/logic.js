// ===================================================================================
//  LOGIC.JS - Main Game Logic Handler (Corrected Version 2)
// ===================================================================================

// =================================================
//  HELPER FUNCTIONS (Directly translated from PHP)
// =================================================

/**
 * Returns the row index number for a given card from the 'fullList'.
 * @param {string} card - The name of the card (e.g., 'Green', 'Wrench').
 * @returns {number} - The index of the card in the full list.
 */
function getRow(card) {
    const fullList = JSON.parse(sessionStorage.getItem('fullList') || '[]');
    return fullList.indexOf(card);
}

/**
 * Modifies the game table to mark a player as having a specific card.
 * It sets the player's column to 'O' and all other columns in that row to 'X'.
 * @param {number} row - The row index of the card to mark.
 * @param {number} col - The column index of the player who has the card.
 */
function eliminateHorizontally(row, col) {
    const table = JSON.parse(sessionStorage.getItem('table') || '[]');
    const players = JSON.parse(sessionStorage.getItem('players') || '[]');
    if (table.length > row && row > -1) {
        // Loop through all columns (Envelope is 0, Player 1 is 1, etc.)
        for (let i = 0; i <= players.length; i++) {
            table[row][i] = 'X';
        }
        table[row][col] = 'O';
        sessionStorage.setItem('table', JSON.stringify(table));
    }
}

/**
 * Eliminates all 3 cards from a guess for a specific player column.
 * Used for players who were asked but did not show a card.
 * @param {number} col - The column index of the player.
 * @param {Guess} guess - The Guess object containing the 3 cards.
 */
function currentGuessElim(col, guess) {
    const table = JSON.parse(sessionStorage.getItem('table') || '[]');
    table[getRow(guess.getSuspect())][col] = 'X';
    table[getRow(guess.getWeapon())][col] = 'X';
    table[getRow(guess.getRoom())][col] = 'X';
    sessionStorage.setItem('table', JSON.stringify(table));
}

/**
 * FINAL CORRECTED VERSION: Runs elimination logic for players between the guesser and shower.
 * @param {number} currentPlayer - The 1-based index of the player who made the guess.
 * @param {number} showPlayer - The 1-based index of the player who showed a card (or 0).
 * @param {Guess} guess - The Guess object.
 */
function guessElims(currentPlayer, showPlayer, guess) {
    const players = JSON.parse(sessionStorage.getItem('players') || '[]');
    const playerCount = players.length;
    if (playerCount === 0) return;

    // Start with the player who just guessed. We will advance to the next player inside the loop.
    let playerToTest = currentPlayer;

    // We only need to loop a maximum of (playerCount - 1) times.
    for (let i = 0; i < playerCount - 1; i++) {
        // Advance to the next player in the circle.
        playerToTest = (playerToTest % playerCount) + 1;

        // If we've reached the player who showed the card, we can stop.
        if (showPlayer !== 0 && playerToTest === showPlayer) {
            break;
        }

        // If we get here, it means 'playerToTest' was asked and did NOT have a card.
        // Mark the three cards as 'X' for this player's column.
        currentGuessElim(playerToTest, guess);
    }
}

/**
 * Checks the table for any rows where all player columns are 'X'.
 * If found, it marks the 'Envelope' column for that row as 'O'.
 */
function checkTable() {
    const table = JSON.parse(sessionStorage.getItem('table') || '[]');
    const players = JSON.parse(sessionStorage.getItem('players') || '[]');
    if (!table.length) return;

    for (let i = 0; i < table.length; i++) {
        let allX = true;
        // Check all player columns (from index 1 to players.length)
        for (let j = 1; j <= players.length; j++) {
            if (table[i][j] !== 'X') {
                allX = false;
                break;
            }
        }
        if (allX) {
            table[i][0] = 'O'; // Mark the envelope column
        }
    }
    sessionStorage.setItem('table', JSON.stringify(table));
}

/**
 * Placeholder for future logic to re-evaluate past guesses.
 */
function checkGuesses() {
    console.log("Checking previous guesses...");
}


// =================================================
//  MAIN FORM HANDLERS
// =================================================

/**
 * Handles the submission of the initial setup form from index.html.
 */
function handleSetupForm(event) {
    event.preventDefault();
    sessionStorage.clear();

    const suspects = ['Green', 'Mustard', 'Peacock', 'Plum', 'Scarlet', 'White'];
    const weapons = ['Wrench', 'Candlestick', 'Dagger', 'Pistol', 'Leadpipe', 'Rope'];
    const rooms = ['Bathroom', 'Office', 'Dining Room', 'Game Room', 'Garage', 'Bedroom', 'Living Room', 'Kitchen', 'Courtyard'];
    const fullList = [...suspects, ...weapons, ...rooms];

    sessionStorage.setItem('suspects', JSON.stringify(suspects));
    sessionStorage.setItem('weapons', JSON.stringify(weapons));
    sessionStorage.setItem('rooms', JSON.stringify(rooms));
    sessionStorage.setItem('fullList', JSON.stringify(fullList));

    const form = event.target;
    const playerCount = parseInt(form.playerCount.value, 10);
    const players = [];
    for (let i = 1; i <= playerCount; i++) {
        players.push(`Player ${i}`);
    }
    sessionStorage.setItem('players', JSON.stringify(players));

    const playerCards = [];
    const cardInputs = form.querySelectorAll('select[name^="card"]');
    cardInputs.forEach(select => {
        if (select.value) {
            playerCards.push(select.value);
        }
    });
    sessionStorage.setItem('playerCards', JSON.stringify(playerCards));

    const table = [];
    for (let i = 0; i < fullList.length; i++) {
        const row = new Array(playerCount + 1).fill('-'); // +1 for the envelope column
        table.push(row);
    }
    sessionStorage.setItem('table', JSON.stringify(table));

    // Player 1 (the user) is in column 1.
    playerCards.forEach(card => {
        const cardRow = getRow(card);
        eliminateHorizontally(cardRow, 1);
    });
    
    checkTable();

    window.location.href = 'game.html';
}

/**
 * Handles the submission of a guess from the form on game.html.
 */
function handleGuessForm(event) {
    event.preventDefault();

    const form = event.target;
    const player = parseInt(form.player.value, 10);
    const suspect = form.suspect.value;
    const weapon = form.weapon.value;
    const room = form.room.value;
    const showPlayer = parseInt(form.showPlayer.value, 10) || 0;
    const shownCard = form.shownCard.value;

    // --- LOGIC BRANCH 1: A specific card was shown to you (Player 1) ---
    // This is the most direct information. We know exactly who has exactly what card.
    if (shownCard) {
        const cardRow = getRow(shownCard);
        // `showPlayer` is the column index for the player who showed the card.
        eliminateHorizontally(cardRow, showPlayer);
    }

    const thisGuess = new Guess(suspect, weapon, room, player);
    const guesses = JSON.parse(sessionStorage.getItem('Guesses') || '[]');
    guesses.push(thisGuess);
    sessionStorage.setItem('Guesses', JSON.stringify(guesses));

    // --- LOGIC BRANCH 2: Mark 'X' for players who were asked but had none of the 3 cards. ---
    // This function will now correctly iterate between the guesser and the shower.
    // If showPlayer is 2 and the guesser is 1, this loop will correctly do nothing.
    // If showPlayer is 0, this loop will correctly mark X for all other players.
    guessElims(player, showPlayer, thisGuess);
    
    // --- FINAL CHECKS: Deduce any new information from the changes we just made. ---
    checkTable();
    checkGuesses();
    
    window.location.reload();
}


// =================================================
//  EVENT LISTENERS
// =================================================

document.addEventListener('DOMContentLoaded', () => {
    const setupForm = document.getElementById('clueForm'); 
    if (setupForm) {
        setupForm.addEventListener('submit', handleSetupForm);
    }

    const guessForm = document.getElementById('guessForm');
    if (guessForm) {
        guessForm.addEventListener('submit', handleGuessForm);
    }
});