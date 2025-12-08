document.addEventListener('DOMContentLoaded', () => {
    const mobileMenuElement = document.getElementById('mobileMenu'); // Getting menu via his ID ( only for mobile )
    const menuLinks = document.querySelectorAll('#mobileMenu .nav-link'); // Getting all nav links on this menu.

    menuLinks.forEach(link => { // Loop on those links
        link.addEventListener('click', (e) => { // Add event for each link
            const targetId = link.getAttribute('href'); // Get href link attribute
            if (targetId && targetId.startsWith('#') && targetId !== '#') { // Check if it's a section and not general ( # )
                e.preventDefault(); // Cancel default action

                if (window.bootstrap) { // Check if it's bootstrap then if it is, close menu
                    const bsOffcanvas = bootstrap.Offcanvas.getInstance(mobileMenuElement);
                    if (bsOffcanvas) {
                        bsOffcanvas.hide();
                    }
                }

                const targetSection = document.querySelector(targetId); // Manually scroll to section
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

    window.addEventListener('activate.bs.scrollspy', (e) => {

        const activeLink = e.relatedTarget; // Current <a> element activated by Boostrap Spy
        const targetId = activeLink.getAttribute('href'); // Get href attribute

        menuLinks.forEach(link => {
            link.classList.remove('active'); // Remove all active classes
        });

        const mobileLink = document.querySelector(`#mobile-nav a[href="${targetId}"]`); // Get current link on mobile version
        if (mobileLink) {
            mobileLink.classList.add('active'); // Add class to current location
        }
    });
});