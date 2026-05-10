const contactForm = document.getElementById('contactForm');
if (contactForm) {
    contactForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const form = e.target;
        const btn = form.querySelector('button[type="submit"]');
        const originalBtnText = btn.innerHTML;

        if (document.getElementById('honeypot')?.value.length > 0) {
            return;
        }

        const showToast = (message, isSuccess = true) => {
            const toastElement = document.getElementById('liveToast');
            const toastMessage = document.getElementById('toastMessage');
            const progressBar = document.getElementById('toastProgress');

            toastElement.classList.remove('text-bg-success', 'text-bg-danger');

            toastElement.classList.add(isSuccess ? 'text-bg-success' : 'text-bg-danger');

            progressBar.style.animation = 'none';
            void progressBar.offsetWidth;
            progressBar.style.animation = 'progress-shrink 5s linear forwards';

            toastMessage.textContent = message;

            const toast = new bootstrap.Toast(toastElement, {
                autohide: true,
                delay: 5000,
                pauseOnHover: false
            });

            toast.show();
        };

        const formData = new FormData(form);
        formData.append('action', 'send_contact');

        try {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Envoi en cours...';
            const response = await fetch('http://localhost:8080/api/contact', {
                method: 'POST',
                body: formData
            });

            const text = await response.text();
            try {
                const res = JSON.parse(text);
                const author = res["author"] ?? "None";
                const error = res["message"] ?? "None";
                if (res.success) {
                    showToast(`Merci ${author}. Votre message a été envoyé !`, true);
                    form.reset();
                } else {
                    showToast(`Erreur: ${error}`, false);
                }
            } catch (e) {
                console.error("Réponse serveur brute :", text);
                showToast("Le serveur a renvoyé une réponse invalide.", false);
            }

        } catch (error) {
            console.error('Erreur:', error);
            showToast("Impossible de contacter le serveur.", false);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalBtnText;
        }
    });
}