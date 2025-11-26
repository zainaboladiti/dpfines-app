// Mobile menu toggle


document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');

    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
            const icon = this.querySelector('i');
            if (mobileMenu.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (mobileMenu && mobileMenuToggle) {
            if (!mobileMenu.contains(event.target) &&
                !mobileMenuToggle.contains(event.target) &&
                mobileMenu.classList.contains('active')) {
                mobileMenu.classList.remove('active');
                const icon = mobileMenuToggle.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }
    });

    // Animate bars on page load
    animateBars();
});

// Animate chart bars
function animateBars() {
    const bars = document.querySelectorAll('.bar-fill');
    bars.forEach((bar, index) => {
        const width = bar.style.width;
        bar.style.width = '0';
        setTimeout(() => {
            bar.style.width = width;
        }, index * 100);
    });
}

// Form validation for newsletter
const newsletterForm = document.querySelector('.newsletter-form');
if (newsletterForm) {
    newsletterForm.addEventListener('submit', function(e) {
        const email = this.querySelector('input[type="email"]').value;
        if (!validateEmail(email)) {
            e.preventDefault();
            alert('Please enter a valid email address');
        }
    });
}

// Email validation
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Search form enhancements
const searchForm = document.querySelector('.search-form');
if (searchForm) {
    const searchInput = searchForm.querySelector('input[name="search"]');

    // Clear button for search
    if (searchInput && searchInput.value) {
        // Add clear functionality if needed
    }
}
