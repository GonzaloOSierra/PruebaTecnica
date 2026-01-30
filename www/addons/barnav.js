document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('sidebar');
  const content = document.getElementById('content');
  const toggleBtn = document.getElementById('sidebarToggle');

  console.log({ sidebar, content, toggleBtn });

  if (!sidebar || !content || !toggleBtn) {
    console.error('No se encontraron los elementos del sidebar o toggle');
    return; // evitar errores posteriores
  }

  const collapsed = localStorage.getItem('sidebarCollapsed') === 'true';
  if (collapsed) {
    sidebar.classList.add('collapsed');
    content.classList.add('expanded');
    toggleBtn.style.left = '10px';
  } else {
    toggleBtn.style.left = '260px';
  }

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
    content.classList.toggle('expanded');

    if (sidebar.classList.contains('collapsed')) {
      toggleBtn.style.left = '10px';
    } else {
      toggleBtn.style.left = '260px';
    }

    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
  });
});

window.onerror = function(msg, url, lineNo, columnNo, error) {
  console.log(`Error JS: ${msg} en ${url}:${lineNo}:${columnNo}`);
};
