document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.querySelector('tbody');
    const thx = document.querySelectorAll('th');
    const trxb = tbody.querySelectorAll('tr');

    const searchInput = document.getElementById('searchInput');
    const categorySelect = document.getElementById('categorySelect');
    const specsTable = document.getElementById('specsTable');
    const rows = specsTable.getElementsByTagName('tr');

    // Map des colonnes pour le filtrage par catégorie
    const columnMap = {
        os: 0,
        version: 1,
        cpu: 2,
        cores: 3,
        threads: 4,
        gpu: 5,
        vram: 6,
        ram: 7
    };

    let currentSortColumn = -1;
    let currentSortDirection = null; // null, 'asc', 'desc'

    // Tri des lignes
    thx.forEach((th, index) => {
        th.addEventListener('click', () => {
            const isAscending = currentSortColumn === index && currentSortDirection === 'asc';

            // Met à jour la direction et la colonne de tri actuelle
            currentSortDirection = isAscending ? 'desc' : 'asc';
            currentSortColumn = index;

            // Trie les lignes
            const sortedRows = Array.from(trxb).sort((row1, row2) => {
                const tdValue = (row, idx) => row.children[idx].textContent.trim();
                const v1 = tdValue(row1, index);
                const v2 = tdValue(row2, index);

                if (!isNaN(parseFloat(v1)) && !isNaN(parseFloat(v2))) {
                    return isAscending ? v2 - v1 : v1 - v2;
                }
                return isAscending
                    ? v2.localeCompare(v1)
                    : v1.localeCompare(v2);
            });

            // Réinsère les lignes triées
            sortedRows.forEach(row => tbody.appendChild(row));
        });
    });

    // Filtrage des lignes
    searchInput.addEventListener('keyup', filterTable);
    categorySelect.addEventListener('change', filterTable);

    function filterTable() {
        const filter = searchInput.value.toLowerCase();
        const category = categorySelect.value;

        for (let i = 0; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            let match = false;

            if (category === "all") {
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].textContent.toLowerCase().includes(filter)) {
                        match = true;
                        break;
                    }
                }
            } else {
                const columnIndex = columnMap[category];
                if (cells[columnIndex] && cells[columnIndex].textContent.toLowerCase().includes(filter)) {
                    match = true;
                }
            }

            rows[i].style.display = match ? '' : 'none';
        }
    }
});
