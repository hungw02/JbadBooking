const TeammateFinder = {
    init: function() {
        this.setupEventListeners();
        this.initializeFilters();
    },

    setupEventListeners: function() {
        // Search form submission
        const searchForm = document.getElementById('search-form');
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(searchForm);
                const searchParams = new URLSearchParams(formData);
                
                window.location.href = `${window.location.pathname}?${searchParams.toString()}`;
            });
        }

        // Clear filters button
        const clearFiltersBtn = document.getElementById('clear-filters');
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', function() {
                window.location.href = window.location.pathname;
            });
        }

        // Delete teammate profile confirmation
        const deleteBtn = document.getElementById('delete-profile');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Bạn có chắc chắn muốn xóa hồ sơ tìm đồng đội của mình?')) {
                    document.getElementById('delete-form').submit();
                }
            });
        }
    },

    initializeFilters: function() {
        // Get current URL params for pre-selecting filters
        const urlParams = new URLSearchParams(window.location.search);
        
        // Set search input value
        const searchInput = document.getElementById('search-input');
        if (searchInput && urlParams.has('search')) {
            searchInput.value = urlParams.get('search');
        }
        
        // Set skill level dropdown
        const skillLevelSelect = document.getElementById('skill-level');
        if (skillLevelSelect && urlParams.has('skill_level')) {
            skillLevelSelect.value = urlParams.get('skill_level');
        }
    }
};

// Initialize module when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    TeammateFinder.init();
});

// Sử dụng CommonJS thay vì ES Module
module.exports = TeammateFinder; 