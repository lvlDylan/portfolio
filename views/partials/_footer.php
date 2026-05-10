<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="liveToast" class="toast hide align-items-center border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body fw-bold" id="toastMessage"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="progress" style="height: 3px; border-radius: 0; background-color: rgba(255,255,255,0.3);">
            <div id="toastProgress" class="progress-bar bg-white" role="progressbar"></div>
        </div>
    </div>
</div>

<footer class="py-5 mt-5 border-top">
    <div class="container text-center">
        <p class="fw-medium mb-1">&copy; <?= date('Y') ?> Dylan Lavieille</p>
        <div class="d-flex justify-content-center align-items-center gap-2">
            <span class="text-muted small">Portfolio</span>
            <span class="text-muted">•</span>
            <button type="button" class="btn-legal" data-bs-toggle="modal" data-bs-target="#legalModal">
                <i class="bi bi-shield-lock me-1"></i>Mentions Légales
            </button>
        </div>
    </div>
</footer>