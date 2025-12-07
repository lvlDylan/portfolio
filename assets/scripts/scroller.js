document.addEventListener('DOMContentLoaded', () => {
    const mobileMenuElement = document.getElementById('mobileMenu');
    const menuLinks = document.querySelectorAll('#mobileMenu .nav-link');

    menuLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            const targetId = link.getAttribute('href');
            if (targetId && targetId.startsWith('#') && targetId !== '#') {
                e.preventDefault();

                if (window.bootstrap) {
                    const bsOffcanvas = bootstrap.Offcanvas.getInstance(mobileMenuElement);
                    if (bsOffcanvas) {
                        bsOffcanvas.hide();
                    }
                }

                const targetSection = document.querySelector(targetId);
                if (targetSection) {
                    setTimeout(() => {
                        targetSection.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }, 300);
                }
            }
        });
    });
});