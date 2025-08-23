document.addEventListener('DOMContentLoaded', () => {
    // --- PART 1: DYNAMIC PAGE POPULATION ---

    // Helper function to safely parse JSON from sessionStorage.
    const getSessionData = (key) => {
        const data = sessionStorage.getItem(key);
        // Return parsed data or an empty array as a fallback.
        return data ? JSON.parse(data) : [];
    };

    // Retrieve all necessary game data from the session.
    const suspects = getSessionData('suspects');
    const weapons = getSessionData('weapons');
    const rooms = getSessionData('rooms');
    const players = getSessionData('players');
    const fullList = getSessionData('fullList');
    const tableData = getSessionData('table');

    // Function to populate a <select> dropdown.
    const populateSelect = (selectId, optionsArray) => {
        const selectElement = document.getElementById(selectId);
        if (selectElement) {
            optionsArray.forEach(option => {
                const optionElement = document.createElement('option');
                optionElement.value = option;
                optionElement.textContent = option;
                selectElement.appendChild(optionElement);
            });
        }
    };

    // Populate the guess dropdowns.
    populateSelect('suspectSelect', suspects);
    populateSelect('weaponSelect', weapons);
    populateSelect('roomSelect', rooms);

    // Function to generate the main results table.
    const generateTable = () => {
        const tableHeader = document.getElementById('tableHeader');
        const tableBody = document.getElementById('tableBody');

        if (!tableHeader || !tableBody || players.length === 0) return;

        // Build the table header row.
        let headerHTML = '<th></th><th>Envelope</th>';
        players.forEach(player => {
            headerHTML += `<th>${player}</th>`;
        });
        tableHeader.innerHTML = headerHTML;

        // Build the table body rows.
        let bodyHTML = '';
        fullList.forEach((item, rowIndex) => {
            bodyHTML += `<tr><th>${item}</th>`;
            // Envelope column + all player columns.
            for (let colIndex = 0; colIndex <= players.length; colIndex++) {
                const cellValue = (tableData[rowIndex] && tableData[rowIndex][colIndex] !== undefined) ? tableData[rowIndex][colIndex] : '-';
                bodyHTML += `<td>${cellValue}</td>`;
            }
            bodyHTML += `</tr>`;
        });
        tableBody.innerHTML = bodyHTML;
    };

    // Initial drawing of the table when the page loads.
    generateTable();

    // Attach logic to the "Setup Page" link to clear the session before navigating.
    const setupLink = document.getElementById('setupLink');
    if(setupLink){
        setupLink.addEventListener('click', (event) => {
            event.preventDefault();
            sessionStorage.clear();
            window.location.href = setupLink.href;
        });
    }

    // --- PART 2: EXTRA FIELD VISIBILITY LOGIC ---

    const suspectSelect = document.getElementById("suspectSelect");
    const weaponSelect = document.getElementById("weaponSelect");
    const roomSelect = document.getElementById("roomSelect");
    const playerInput = document.getElementById("playerInput");
    const showPlayerInput = document.getElementById("showPlayerInput");
    const extraField = document.getElementById("extraField");
    const shownCardSelect = document.getElementById("shownCardSelect");

    function updateShownCardOptions() {
        const s = suspectSelect.value;
        const w = weaponSelect.value;
        const r = roomSelect.value;
        if (!s || !w || !r) return; // Don't update unless all 3 are selected.

        // Re-populate the 'shownCard' dropdown with the current guess.
        shownCardSelect.innerHTML = "<option value=''></option>";
        shownCardSelect.innerHTML += `<option value="${s}">${s}</option>`;
        shownCardSelect.innerHTML += `<option value="${w}">${w}</option>`;
        shownCardSelect.innerHTML += `<option value="${r}">${r}</option>`;
    }

    function checkExtraFieldConditions() {
        const playerVal = parseInt(playerInput.value, 10);
        const showPlayerVal = parseInt(showPlayerInput.value, 10);

        // Show the extra field only if Player 1 is guessing and someone else is showing a card.
        const shouldShow = playerVal === 1 && showPlayerVal > 1;
        extraField.style.display = shouldShow ? 'block' : 'none';

        if (shouldShow) {
            updateShownCardOptions();
        }
    }

    // Attach event listeners to the form inputs.
    playerInput.addEventListener('input', checkExtraFieldConditions);
    showPlayerInput.addEventListener('input', checkExtraFieldConditions);
    suspectSelect.addEventListener('change', updateShownCardOptions);
    weaponSelect.addEventListener('change', updateShownCardOptions);
    roomSelect.addEventListener('change', updateShownCardOptions);
});