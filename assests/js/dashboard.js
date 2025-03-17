// Sidebar toggle functionaliteit
const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebar-toggle');

sidebarToggle.addEventListener('click', () => {
    sidebar.classList.toggle('open');
    document.querySelector('.main-content').style.marginLeft = sidebar.classList.contains('open') ? '250px' : '0';
});
