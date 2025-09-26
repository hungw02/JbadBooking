//Header
const $ = document.querySelector.bind(document);
const $$ = document.querySelectorAll.bind(document);

const tabs = $$(".nav-item");
const line = $(".nav-list .line");

// Khởi tạo vị trí ban đầu của line dựa trên active class từ server
const tabActive = $(".nav-item.active") || tabs[0];
if (tabActive) {
    line.style.left = tabActive.offsetLeft + "px";
    line.style.width = tabActive.offsetWidth + "px";
}

// Xử lý khi click vào tab (chỉ cho các tab không phải là form đăng xuất)
tabs.forEach((tab) => {
    // Bỏ qua nếu tab chứa form (đăng xuất)
    if (!tab.querySelector("form")) {
        tab.onclick = function (e) {
            // Không cần remove active class vì sẽ được xử lý khi load trang mới
            line.style.left = this.offsetLeft + "px";
            line.style.width = this.offsetWidth + "px";
        };
    }
});

// Sidebar
function setActive(element) {
    // Remove active class from all items
    document.querySelectorAll(".sidebar-item").forEach((item) => {
        item.classList.remove("active");
    });

    // Add active class to clicked item
    element.classList.add("active");
}

// Slide
document.addEventListener("DOMContentLoaded", function () {
    const slides = document.querySelectorAll(".slider-content");
    const tabsContainer = document.querySelector(".slide-tabs");
    const tabs = document.querySelectorAll(".slide-tab");
    const dots = document.querySelectorAll(".dot");
    let currentSlide = 0;
    let isAnimating = false;
    let autoSlideInterval;

    // Check if slider elements exist before running slider code
    if (slides.length === 0 || !tabsContainer || tabs.length === 0 || dots.length === 0) {
        // Exit early if slider elements don't exist
        return;
    }

    function updateTabsOrder(currentIndex) {
        const tabsArray = Array.from(tabs);
        tabsContainer.innerHTML = "";

        // Reorder tabs starting from next slide
        for (let i = 1; i < slides.length; i++) {
            let index = (currentIndex + i) % slides.length;
            tabsArray[index].classList.remove("active");
            tabsContainer.appendChild(tabsArray[index]);
        }
    }

    function updateSlides(index) {
        // Make sure index is valid
        if (isAnimating || currentSlide === index || index >= slides.length) return;
        isAnimating = true;

        const currentSlideEl = slides[currentSlide];
        const nextSlideEl = slides[index];

        dots.forEach((dot) => dot.classList.remove("active"));
        // Check if dots[index] exists before using it
        if (dots[index]) {
            dots[index].classList.add("active");
        }

        updateTabsOrder(index);

        currentSlideEl.classList.add("leaving");

        nextSlideEl.classList.add("active");

        setTimeout(() => {
            currentSlideEl.classList.remove("active", "leaving");
            isAnimating = false;
            currentSlide = index;
        }, 600);
    }

    tabs.forEach((tab, index) => {
        tab.addEventListener("click", () => updateSlides(index));
    });

    dots.forEach((dot, index) => {
        dot.addEventListener("click", () => updateSlides(index));
    });

    autoSlideInterval = setInterval(() => {
        if (!isAnimating) {
            let newIndex = (currentSlide + 1) % slides.length;
            updateSlides(newIndex);
        }
    }, 2500);

    const sliderContainer = document.querySelector(".slider-container");
    if (sliderContainer) {
        sliderContainer.addEventListener("mouseenter", () =>
            clearInterval(autoSlideInterval)
        );
        sliderContainer.addEventListener("mouseleave", () => {
            autoSlideInterval = setInterval(() => {
                if (!isAnimating) {
                    let newIndex = (currentSlide + 1) % slides.length;
                    updateSlides(newIndex);
                }
            }, 2500);
        });
    }

    // Initialize tabs order
    updateTabsOrder(0);
});
