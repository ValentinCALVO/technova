<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planning de Présentiel des Collaborateurs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4f8;
        }
        header {
            background-color: #0056b3;
            color: white;
            text-align: center;
            padding: 1rem;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 1rem;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .calendar-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .calendar-nav button {
            padding: 0.5rem 1rem;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .calendar-nav button:hover {
            background-color: #003b75;
        }
        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1rem;
            text-align: center;
        }
        .calendar-header {
            font-weight: bold;
            color: #333;
        }
        .day {
            padding: 10px;
            background-color: #fff;
            border: 1px solid #ddd;
            cursor: pointer;
            transition: background-color 0.3s;
            position: relative;
        }
        .day:hover {
            background-color: #e6f7ff;
        }
        .day.selected {
            background-color: #80c7ff;
        }
        .day .info-icon {
            position: absolute;
            top: 5px;
            right: 5px;
            font-size: 20px;
            color: #007bff;
            cursor: pointer;
        }
        .form-container {
            margin-top: 1rem;
            display: none;
        }
        .form-container input {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-container button {
            padding: 0.5rem 1rem;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: none;
            width: 300px;
            border-radius: 5px;
        }
        .popup h3 {
            margin-top: 0;
        }
        .popup ul {
            list-style: none;
            padding: 0;
        }
        .popup li {
            padding: 5px 0;
        }
        .popup button {
            margin-top: 10px;
            background-color: #ff5c5c;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .popup button:hover {
            background-color: #e04343;
        }
    </style>
</head>
<body>

<header>
    <h1>Interface SpaceIQ dédiée à TechNova </p><font size="4pt">(Gestion des présences sur site)</font></h1>
</header>

<div class="container">
    <div class="calendar-nav">
        <button id="prevMonthBtn">&lt; Mois Précédent</button>
        <h2 id="monthYearDisplay">Novembre 2024</h2>
        <button id="nextMonthBtn">Mois Suivant &gt;</button>
    </div>

    <!-- Sélection du département -->
    <div>
        <label for="departmentFilter">Filtrer par département :</label>
        <select id="departmentFilter">
            <option value="all">Tous les départements</option>
            <option value="DG">Direction générale</option>
            <option value="RH">Ressources Humaines</option>
            <option value="Commercial">Commercial</option>
            <option value="Tech">Technique</option>
            <option value="Support">Support Client</option>
            <option value="Services">Services généraux</option>
            <option value="Admin">Services administratifs et financiers</option>
            <option value="Management">Management intermédiaire</option>
        </select>
    </div>

    <div id="calendar" class="calendar"></div>

    <div id="formContainer" class="form-container">
        <h3>Inscrire un collaborateur</h3>
        <input type="text" id="employeeName" placeholder="Nom du collaborateur">
        <select id="employeeDepartment">
            <option value="DG">Direction générale</option>
            <option value="RH">Ressources Humaines</option>
            <option value="Commercial">Commercial</option>
            <option value="Tech">Technique</option>
            <option value="Support">Support Client</option>
            <option value="Services">Services généraux</option>
            <option value="Admin">Services administratifs et financiers</option>
            <option value="Management">Management intermédiaire</option>
        </select>
        <button id="submitBtn">Enregistrer</button>
    </div>
</div>

<!-- Popup pour afficher les collaborateurs présents -->
<div id="popup" class="popup">
    <h3>Collaborateurs présents</h3>
    <ul id="popupList"></ul>
    <button id="closePopupBtn">Fermer</button>
</div>

<script>
    const departments = {
        DG: { name: 'Direction générale', color: '#0056b3' },
        RH: { name: 'Ressources Humaines', color: '#28a745' },
        Commercial: { name: 'Commercial', color: '#ffc107' },
        Tech: { name: 'Technique', color: '#17a2b8' },
        Support: { name: 'Support Client', color: '#6c757d' },
        Services: { name: 'Services généraux', color: '#007bff' },
        Admin: { name: 'Services administratifs et financiers', color: '#fd7e14' },
        Management: { name: 'Management intermédiaire', color: '#6610f2' }
    };

    let currentDate = new Date('2024-11-25');  // 25 novembre 2024
    const calendarElement = document.getElementById('calendar');
    const monthYearDisplay = document.getElementById('monthYearDisplay');
    const prevMonthBtn = document.getElementById('prevMonthBtn');
    const nextMonthBtn = document.getElementById('nextMonthBtn');
    const departmentFilter = document.getElementById('departmentFilter');
    const formContainer = document.getElementById('formContainer');
    const employeeNameInput = document.getElementById('employeeName');
    const employeeDepartmentInput = document.getElementById('employeeDepartment');
    const submitBtn = document.getElementById('submitBtn');
    const popup = document.getElementById('popup');
    const popupList = document.getElementById('popupList');
    const closePopupBtn = document.getElementById('closePopupBtn');
    const maxPerDay = 50;
    let selectedDay = null;
    let dayData = {};  // { date: { department: [names] } }

    // Exemple de collaborateurs déjà inscrits pour certains jours
    const exampleCollaborators = {
        '2024-11-27': {
            DG: ['Jean Dupont', 'Alice Martin'],
            RH: ['Sophie Lemoine'],
            Tech: ['Marc Bernard', 'Élise Gauthier']
        },
        '2024-11-28': {
            Commercial: ['Pierre Lefevre'],
            Support: ['Clara Ziegler']
        }
    };

    // Fonction pour afficher le calendrier pour un mois donné
    function generateCalendar() {
        const month = currentDate.getMonth();
        const year = currentDate.getFullYear();
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);

        monthYearDisplay.textContent = `${firstDay.toLocaleString('fr-FR', { month: 'long' })} ${year}`;

        // Calcul du nombre de jours dans le mois
        const daysInMonth = lastDay.getDate();

        // Calcul du jour de la semaine pour le 1er du mois
        const firstDayOfWeek = firstDay.getDay(); // 0: dimanche, 1: lundi, ..., 6: samedi

        // Nettoyer le calendrier avant de le remplir
        calendarElement.innerHTML = '';

        // Créer l'en-tête (jours de la semaine)
        const daysOfWeek = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
        daysOfWeek.forEach(day => {
            const headerCell = document.createElement('div');
            headerCell.classList.add('calendar-header');
            headerCell.textContent = day;
            calendarElement.appendChild(headerCell);
        });

        // Remplir les cases vides avant le premier jour du mois
        for (let i = 0; i < firstDayOfWeek; i++) {
            const emptyCell = document.createElement('div');
            calendarElement.appendChild(emptyCell);
        }

        // Remplir les cases avec les jours du mois
        for (let day = 1; day <= daysInMonth; day++) {
            const dayCell = document.createElement('div');
            dayCell.classList.add('day');
            dayCell.textContent = day;
            dayCell.dataset.date = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

            // Ajouter une icône "i" si des collaborateurs sont inscrits
            const dateKey = dayCell.dataset.date;
            if (exampleCollaborators[dateKey]) {
                const infoIcon = document.createElement('span');
                infoIcon.classList.add('info-icon');
                infoIcon.textContent = 'i';
                dayCell.appendChild(infoIcon);
            }

            // Ajouter un événement de clic pour afficher les détails
            dayCell.addEventListener('click', () => {
                selectDay(dayCell);
            });

            calendarElement.appendChild(dayCell);
        }
    }

    // Fonction pour sélectionner un jour
    function selectDay(dayCell) {
        if (selectedDay) {
            selectedDay.classList.remove('selected');
        }
        selectedDay = dayCell;
        selectedDay.classList.add('selected');
        formContainer.style.display = 'block';
    }

    // Fonction pour filtrer par département
    departmentFilter.addEventListener('change', () => {
        generateCalendar();
    });

    // Fonction pour naviguer entre les mois
    prevMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        generateCalendar();
    });

    nextMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        generateCalendar();
    });

    // Fonction pour afficher les collaborateurs dans le popup
    calendarElement.addEventListener('click', (e) => {
        if (e.target.classList.contains('info-icon')) {
            const dateKey = e.target.closest('.day').dataset.date;
            if (exampleCollaborators[dateKey]) {
                popupList.innerHTML = '';
                Object.keys(exampleCollaborators[dateKey]).forEach(department => {
                    const departmentName = departments[department].name;
                    const employees = exampleCollaborators[dateKey][department];
                    const departmentItem = document.createElement('li');
                    departmentItem.innerHTML = `<strong>${departmentName}</strong>: ${employees.join(', ')}`;
                    popupList.appendChild(departmentItem);
                });
                popup.style.display = 'block';
            }
        }
    });

    // Fermer le popup
    closePopupBtn.addEventListener('click', () => {
        popup.style.display = 'none';
    });

    // Initialiser le calendrier
    generateCalendar();
</script>

</body>
</html>
