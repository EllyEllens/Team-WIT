document.addEventListener("DOMContentLoaded", function() {
    fetchStudents();
});

// Studenten ophalen uit de database
function fetchStudents() {
    fetch("php/get_students.php")
        .then(response => response.json())
        .then(data => {
            const tableBody = document.querySelector("#studentTable tbody");
            tableBody.innerHTML = "";
            data.forEach((student, index) => {
                let row = `
                    <tr>
                        <td>${student.id}</td>
                        <td>${student.voornaam}</td>
                        <td>${student.achternaam}</td>
                        <td>${student.email}</td>
                        <td>${student.telefoon}</td>
                        <td>
                            <button class="edit-btn" onclick="openEditPopup(${index}, ${student.id})">Bewerken</button>
                            <button class="delete-btn" onclick="deleteStudent(${student.id})">Verwijderen</button>
                        </td>
                    </tr>`;
                tableBody.innerHTML += row;
            });
        });
}

// Bewerken Pop-up openen
function openEditPopup(index, id) {
    document.getElementById("edit-id").value = id;
    document.getElementById("editPopup").style.display = "block";
}

// Student bijwerken
function updateStudent() {
    const id = document.getElementById("edit-id").value;
    const data = {
        id,
        voornaam: document.getElementById("edit-firstName").value,
        achternaam: document.getElementById("edit-lastName").value,
        email: document.getElementById("edit-email").value,
        telefoon: document.getElementById("edit-phoneNumber").value
    };

    fetch("php/update_student.php", {
        method: "POST",
        body: JSON.stringify(data),
        headers: { "Content-Type": "application/json" }
    }).then(() => {
        closeEditPopup();
        fetchStudents();
    });
}

// Student verwijderen
function deleteStudent(id) {
    fetch("php/delete_student.php", {
        method: "POST",
        body: JSON.stringify({ id }),
        headers: { "Content-Type": "application/json" }
    }).then(() => fetchStudents());
}

// Pop-up sluiten
function closeEditPopup() {
    document.getElementById("editPopup").style.display = "none";
}

function deleteStudent(id) {
    if (confirm("Weet je zeker dat je deze persoon wilt verwijderen?")) {
        fetch('student.php', {
            method: 'POST',
            body: JSON.stringify({ id: id }),
            headers: { 'Content-Type': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Gebruiker is succesvol verwijderd!");
                document.getElementById("row-" + id).remove(); // Verwijder de rij zonder pagina te herladen
            } else {
                alert("Er is een fout opgetreden: " + data.error);
            }
        })
        .catch(error => console.error('Fout bij verwijderen:', error));
    }
}

// Sidebar toggle functionaliteit
const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebar-toggle');

sidebarToggle.addEventListener('click', () => {
    sidebar.classList.toggle('open');
    document.querySelector('.main-content').style.marginLeft = sidebar.classList.contains('open') ? '250px' : '0';
});
