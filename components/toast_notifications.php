<?php

?>
<style>
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
        pointer-events: none;
    }

    .toast {
        background: white;
        padding: 1rem 1.25rem;
        border-radius: 12px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 300px;
        max-width: 400px;
        transform: translateX(120%);
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: auto;
        border-left: 4px solid transparent;
    }

    .toast.show {
        transform: translateX(0);
    }

    .toast-success {
        border-left-color: #059669;
    }

    .toast-error {
        border-left-color: #dc2626;
    }

    .toast-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .toast-success .toast-icon {
        background: #dcfce7;
        color: #15803d;
    }

    .toast-error .toast-icon {
        background: #fee2e2;
        color: #b91c1c;
    }

    .toast-content {
        flex: 1;
    }

    .toast-title {
        font-weight: 600;
        font-size: 0.95rem;
        color: #1e293b;
        margin-bottom: 2px;
    }

    .toast-message {
        font-size: 0.85rem;
        color: #64748b;
        line-height: 1.4;
    }

    .toast-close {
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 4px;
        transition: color 0.2s;
    }

    .toast-close:hover {
        color: #475569;
    }
</style>

<div class="toast-container" id="toastContainer"></div>

<script>
    function showToast(title, message, type = 'success') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        const iconHtml = type === 'success' 
            ? '<i data-lucide="check" style="width:16px;height:16px;"></i>' 
            : '<i data-lucide="alert-circle" style="width:16px;height:16px;"></i>';

        toast.innerHTML = `
            <div class="toast-icon">${iconHtml}</div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i data-lucide="x" style="width:16px;height:16px;"></i>
            </button>
        `;

        container.appendChild(toast);
        
        if (window.lucide) {
            lucide.createIcons();
        }

        requestAnimationFrame(() => {
            toast.classList.add('show');
        });

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                toast.remove();
            }, 400);
        }, 5000);
    }

    <?php if (isset($_SESSION['msg'])): ?>
        document.addEventListener('DOMContentLoaded', () => {
            showToast('Success', <?= json_encode($_SESSION['msg']) ?>, 'success');
        });
        <?php unset($_SESSION['msg']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        document.addEventListener('DOMContentLoaded', () => {
            showToast('Error', <?= json_encode($_SESSION['error']) ?>, 'error');
        });
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
</script>
