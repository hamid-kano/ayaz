// Delete Modal Helper
function showDeleteModal(deleteUrl, itemName = 'العنصر') {
    // Update modal content if needed
    const modal = document.querySelector('[x-data*="open: false"]');
    if (modal) {
        const message = modal.querySelector('p');
        if (message) {
            message.textContent = `هل أنت متأكد من أنك تريد حذف ${itemName}؟ لا يمكن التراجع عن هذا الإجراء.`;
        }
    }
    
    // Show modal
    window.dispatchEvent(new CustomEvent('delete-modal'));
    
    // Handle confirmation
    const handleConfirm = (e) => {
        if (deleteUrl.includes('form-')) {
            // Submit form
            document.getElementById(deleteUrl.replace('#', '')).submit();
        } else {
            // Navigate to URL
            window.location.href = deleteUrl;
        }
        window.removeEventListener('confirm-delete', handleConfirm);
    };
    
    window.addEventListener('confirm-delete', handleConfirm);
}

// Replace all confirm dialogs
document.addEventListener('DOMContentLoaded', function() {
    // Replace onclick confirm
    document.querySelectorAll('[onclick*="confirm"]').forEach(element => {
        const onclick = element.getAttribute('onclick');
        const match = onclick.match(/confirm\(['"]([^'"]+)['"]\)/);
        if (match) {
            const confirmText = match[1];
            element.removeAttribute('onclick');
            
            element.addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.getAttribute('href') || this.closest('form')?.getAttribute('action');
                showDeleteModal(href, 'العنصر');
            });
        }
    });
});