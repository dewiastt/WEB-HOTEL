// Navbar scroll effect
window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            window.scrollTo({
                top: target.offsetTop - 100,
                behavior: 'smooth'
            });
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const liveSearch = document.getElementById('liveSearch');
    const filterType = document.getElementById('filterType');
    const sortPrice = document.getElementById('sortPrice');
    const roomsContainer = document.getElementById('roomsContainer');
    const noResults = document.getElementById('noResults');
    const resultCount = document.getElementById('count');
    const roomItems = Array.from(document.querySelectorAll('.room-item'));

    function updateResults() {
        let filtered = roomItems;

        // 1. Pencarian real-time
        const query = liveSearch.value.toLowerCase().trim();
        if (query) {
            filtered = filtered.filter(item => 
                item.dataset.search.includes(query)
            );
        }

        // 2. Filter tipe
        const selectedType = filterType.value;
        if (selectedType) {
            filtered = filtered.filter(item => 
                item.dataset.type === selectedType
            );
        }

        // 3. Sort harga
        const sortOrder = sortPrice.value;
        filtered.sort((a, b) => {
            const priceA = parseFloat(a.dataset.price);
            const priceB = parseFloat(b.dataset.price);
            return sortOrder === 'low' ? priceA - priceB : priceB - priceA;
        });

        // Kosongkan container
        roomsContainer.innerHTML = '';

        // Tampilkan hasil
        if (filtered.length > 0) {
            filtered.forEach(item => roomsContainer.appendChild(item));
            noResults.classList.add('d-none');
        } else {
            noResults.classList.remove('d-none');
        }

        // Update counter
        resultCount.textContent = filtered.length;
    }

    // Event listeners
    liveSearch.addEventListener('input', updateResults);
    filterType.addEventListener('change', updateResults);
    sortPrice.addEventListener('change', updateResults);

    // Inisialisasi
    updateResults();
});

document.addEventListener('DOMContentLoaded', function () {
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
});