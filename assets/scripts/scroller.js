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

    const sections = document.querySelectorAll('section'); // Get all section
    const navLinks = document.querySelectorAll('.sidebar .nav-link, #mobileMenu .nav-link'); // Get nav button from sidebar or mobile menu
    window.addEventListener('scroll', () => { // Add event on scrolling
        let current = "";

        const isAtBottom = (window.innerHeight + window.scrollY) >= document.body.offsetHeight - 10; // Check if its bottom of page

        if (isAtBottom) {
            current = sections[sections.length - 1].getAttribute("id"); // Set last section
        } else { // Else get section who is currently watched
            sections.forEach(section => {
                const sectionTop = section.offsetTop;

                if (window.scrollY >= (sectionTop - 150)) {
                    current = section.getAttribute("id");
                }
            });
        }

        navLinks.forEach((link) => {
            link.classList.remove("active"); // remove class active from every link
            if (link.getAttribute("href").includes(current)) {
                link.classList.add("active"); // Add class active for current section
            }
        });
    });
});