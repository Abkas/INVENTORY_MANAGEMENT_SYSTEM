function showConfirmModal(options) {
    const {
        title = 'Are you sure?',
        message = 'This action cannot be undone.',
        icon = '⚠️',
        iconType = 'warning',
        confirmText = 'Confirm',
        confirmClass = 'confirm',
        cancelText = 'Cancel',
        onConfirm = () => { },
        onCancel = () => { }
    } = options;

    const modalHTML = `
        <div class="confirm-modal" id="confirmModal">
            <div class="confirm-modal-content">
                <div class="confirm-modal-icon ${iconType}">
                    ${icon}
                </div>
                <div class="confirm-modal-title">${title}</div>
                <div class="confirm-modal-message">${message}</div>
                <div class="confirm-modal-actions">
                    <button class="confirm-modal-btn cancel" id="modalCancel">${cancelText}</button>
                    <button class="confirm-modal-btn ${confirmClass}" id="modalConfirm">${confirmText}</button>
                </div>
            </div>
        </div>
    `;

    const existingModal = document.getElementById('confirmModal');
    if (existingModal) {
        existingModal.remove();
    }

    document.body.insertAdjacentHTML('beforeend', modalHTML);

    const modal = document.getElementById('confirmModal');
    const confirmBtn = document.getElementById('modalConfirm');
    const cancelBtn = document.getElementById('modalCancel');

    modal.style.display = 'flex';

    confirmBtn.onclick = () => {
        modal.style.display = 'none';
        modal.remove();
        onConfirm();
    };

    cancelBtn.onclick = () => {
        modal.style.display = 'none';
        modal.remove();
        onCancel();
    };

    modal.onclick = (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
            modal.remove();
            onCancel();
        }
    };

    const escapeHandler = (e) => {
        if (e.key === 'Escape') {
            modal.style.display = 'none';
            modal.remove();
            document.removeEventListener('keydown', escapeHandler);
            onCancel();
        }
    };
    document.addEventListener('keydown', escapeHandler);
}
