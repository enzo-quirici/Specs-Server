document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.querySelector('tbody');
    const thx = document.querySelectorAll('th');
    const trxb = tbody.querySelectorAll('tr');

    const searchInput = document.getElementById('searchInput');
    const categorySelect = document.getElementById('categorySelect');
    const specsTable = document.getElementById('specsTable');
    const rows = specsTable.getElementsByTagName('tr');

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
    let currentSortDirection = null;

    thx.forEach((th, index) => {
        th.addEventListener('click', () => {
            const isAscending = currentSortColumn === index && currentSortDirection === 'asc';

            currentSortDirection = isAscending ? 'desc' : 'asc';
            currentSortColumn = index;

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

            sortedRows.forEach(row => tbody.appendChild(row));
        });
    });

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
// Fonction pour activer/désactiver le mode sombre
function toggleDarkMode() {
    const body = document.body;
    const switchInput = document.getElementById('darkModeToggle');
  
    // Si le switch est coché, on active le dark mode
    if (switchInput.checked) {
      body.classList.add('dark-mode');
      localStorage.setItem('darkMode', 'enabled'); // Enregistrer dans le localStorage
    } else {
      body.classList.remove('dark-mode');
      localStorage.setItem('darkMode', 'disabled'); // Enregistrer dans le localStorage
    }
  }
  
  // Vérifier si le dark mode était activé précédemment dans le localStorage
  function checkDarkMode() {
    const darkModeStatus = localStorage.getItem('darkMode');
    const switchInput = document.getElementById('darkModeToggle');
    const body = document.body;
  
    // Si le dark mode était activé, on applique la classe "dark-mode"
    if (darkModeStatus === 'enabled') {
      body.classList.add('dark-mode');
      switchInput.checked = true; // Coche le switch
    } else {
      body.classList.remove('dark-mode');
      switchInput.checked = false; // Décoche le switch
    }
  }
  
  // Initialisation de la page
  document.addEventListener('DOMContentLoaded', () => {
    checkDarkMode(); // Vérifie et applique le dark mode au chargement de la page
  
    // Écouteur d'événements sur le switch
    const switchInput = document.getElementById('darkModeToggle');
    switchInput.addEventListener('change', toggleDarkMode); // Toggle le dark mode lors du changement de l'état du switch
  });
  