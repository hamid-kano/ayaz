<script>
function showDeleteModal(deleteUrl, itemName = 'العنصر', formElement = null) {
    const modal = document.querySelector('[x-data*="open: false"]');
    if (modal) {
        const message = modal.querySelector('p');
        if (message) {
            message.textContent = `هل أنت متأكد من أنك تريد حذف ${itemName}؟ لا يمكن التراجع عن هذا الإجراء.`;
        }
    }
    
    window.dispatchEvent(new CustomEvent('delete-modal'));
    
    const handleConfirm = (e) => {
        if (formElement) {
            formElement.submit();
        } else {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = deleteUrl;
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);
            }
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            document.body.appendChild(form);
            form.submit();
        }
        window.removeEventListener('confirm-delete', handleConfirm);
    };
    
    window.addEventListener('confirm-delete', handleConfirm);
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[onclick*="confirm"]').forEach(element => {
        const onclick = element.getAttribute('onclick');
        const match = onclick.match(/confirm\(['"]([^'"]+)['"]\)/);
        if (match) {
            const confirmText = match[1];
            const originalOnclick = onclick;
            element.removeAttribute('onclick');
            
            element.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                const href = this.getAttribute('href');
                
                if (form) {
                    showDeleteModal(form.action, 'العنصر', form);
                } else if (href) {
                    showDeleteModal(href, 'العنصر');
                }
            });
        }
    });
});
</script>