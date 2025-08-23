document.addEventListener('DOMContentLoaded', () => {
    // Define the card data needed to build the dropdowns.
    const suspects = ['Green', 'Mustard', 'Peacock', 'Plum', 'Scarlet', 'White'];
    const weapons = ['Wrench', 'Candlestick', 'Dagger', 'Pistol', 'Leadpipe', 'Rope'];
    const rooms = ['Bathroom', 'Office', 'Dining Room', 'Game Room', 'Garage', 'Bedroom', 'Living Room', 'Kitchen', 'Courtyard'];
    const allCards = [...suspects, ...weapons, ...rooms];

    // Build the HTML <option> string from the combined array.
    let cardOptions = '';
    allCards.forEach(card => {
        cardOptions += `<option value='${card}'>${card}</option>`;
    });

    // Get the necessary elements from the DOM.
    const cardCountInput = document.getElementById('cardCount');
    const cardInputsDiv = document.getElementById('cardInputs');

    // Attach an event listener to dynamically create the card selectors.
    cardCountInput.addEventListener('input', () => {
        // Use a fallback of 0 if parsing fails.
        const count = parseInt(cardCountInput.value) || 0;

        // Clear any existing dropdowns.
        cardInputsDiv.innerHTML = '';

        // Create and append the new select inputs based on the count.
        for (let i = 1; i <= count; i++) {
            const label = document.createElement('label');
            label.className = 'card-input';
            label.innerHTML = `Card ${i}: <select name="card${i}">${cardOptions}</select><br><br>`;
            cardInputsDiv.appendChild(label);
        }
    });
});