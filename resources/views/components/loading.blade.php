<div id="loading-container" class="active">
    <div class="loading-content">
        <div class="spinner"></div>
        <div class="loading-text">Đang tải, vui lòng chờ...</div>
    </div>
</div>

<script>
    // Function to hide loading screen
    function hideLoading() {
        document.getElementById("loading-container").classList.add("hidden");
        setTimeout(() => {
            document.getElementById("loading-container").style.display = "none";
        }, 500);
    }

    // Function to show loading screen
    function showLoading() {
        document.getElementById('loading-container').classList.remove('hidden');
        document.getElementById('loading-container').style.display = 'flex';
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Hiện loading khi click vào link
        document.addEventListener('click', function(e) {
            const target = e.target.closest('a');
            if (target && !target.getAttribute('href').startsWith('#') && 
                !target.getAttribute('href').startsWith('javascript:') && 
                !e.ctrlKey && !e.metaKey) {
                showLoading();
            }
        });
        
        // Hiện loading khi submit form
        document.addEventListener('submit', function() {
            showLoading();
        });
    });

    // Khi trang load xong, ẩn phần loading
    window.addEventListener("load", hideLoading);
    
    // Handle browser back/forward navigation
    window.addEventListener("popstate", hideLoading);
    
    // Handle page show (includes back-forward cache)
    window.addEventListener("pageshow", function(event) {
        // If the page is loaded from browser cache
        if (event.persisted) {
            hideLoading();
        }
    });
</script>