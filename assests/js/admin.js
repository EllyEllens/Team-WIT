// Dummy data voor studenten, docenten, klassen en aanwezigheid
const data = {
    studentsCount: 0,
    teachersCount: 0,
    classesCount: 0,
    attendanceCount: 0,
    attendanceRecords: [
        { name: 'Geen', contact: 'Geen', present: 'Geen', date: 'Geen' }
    ],
    teachers: [
        { name: 'Nog geen docenten' }
    ]
};

// Vul de dashboard kaarten in
document.getElementById('students-count').textContent = data.studentsCount;
document.getElementById('teachers-count').textContent = data.teachersCount;
document.getElementById('classes-count').textContent = data.classesCount;
document.getElementById('attendance-count').textContent = data.attendanceCount;

// Vul de aanwezigheidstabel in
const tableBody = document.getElementById('attendance-table').getElementsByTagName('tbody')[0];
data.attendanceRecords.forEach(record => {
    const row = tableBody.insertRow();
    row.innerHTML = `
        <td>${record.name}</td>
        <td>${record.contact}</td>
        <td>${record.present}</td>
        <td>${record.date}</td>
    `;
});

// Vul de docentenlijst in
const teachersList = document.getElementById('teachers-list');
data.teachers.forEach(teacher => {
    const listItem = document.createElement('li');
    listItem.textContent = teacher.name;
    teachersList.appendChild(listItem);
});

// Sidebar toggle functionaliteit
const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebar-toggle');

sidebarToggle.addEventListener('click', () => {
    sidebar.classList.toggle('open');
    document.querySelector('.main-content').style.marginLeft = sidebar.classList.contains('open') ? '250px' : '0';
});
