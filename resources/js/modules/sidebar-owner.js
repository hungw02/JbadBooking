// Owner Sidebar
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar-owner');
    const mainContent = document.getElementById('main-content');
    const headerContainer = document.getElementById('header-container');
    
    if (sidebar && mainContent && headerContainer) {
        sidebar.addEventListener('mouseenter', function() {
            mainContent.style.marginLeft = '16rem';
            mainContent.style.maxWidth = 'calc(100% - 16rem)';
            headerContainer.style.marginLeft = '16rem';
            headerContainer.style.width = 'calc(100% - 16rem)';
        });
        
        sidebar.addEventListener('mouseleave', function() {
            mainContent.style.marginLeft = '4rem';
            mainContent.style.maxWidth = 'calc(100% - 4rem)';
            headerContainer.style.marginLeft = '4rem';
            headerContainer.style.width = 'calc(100% - 4rem)';
        });
    }
});