document.addEventListener('DOMContentLoaded', () => {
    // --- Mobile Menu Toggle ---
    const mobileToggle = document.querySelector('.mobile-toggle');
    const body = document.body;

    if (mobileToggle) {
        mobileToggle.addEventListener('click', () => {
            const isOpen = body.classList.toggle('menu-open');
            mobileToggle.setAttribute('aria-expanded', isOpen);
        });
    }

    // --- Dropdown Logic (Desktop & Mobile) ---
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const toggleBtn = dropdown.querySelector('.dropdown-toggle');
        
        toggleBtn.addEventListener('click', (e) => {
            e.preventDefault(); 
            const isActive = dropdown.classList.contains('active');
            
            dropdowns.forEach(d => {
                d.classList.remove('active');
                d.querySelector('.dropdown-toggle').setAttribute('aria-expanded', 'false');
            });

            if (!isActive) {
                dropdown.classList.add('active');
                toggleBtn.setAttribute('aria-expanded', 'true');
            }
        });
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown')) {
            dropdowns.forEach(d => {
                d.classList.remove('active');
                d.querySelector('.dropdown-toggle').setAttribute('aria-expanded', 'false');
            });
        }
    });
});