<script>
document.addEventListener('DOMContentLoaded', function() {
    const notificationBtn = document.querySelector('.notification-btn');
    const dropdown = document.querySelector('.notifications-dropdown');
    
    if (notificationBtn && dropdown) {
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('show');
            console.log('Dropdown toggled, visible:', dropdown.classList.contains('show'));
        });
        
        // Load initial count
        fetch('{{ route("notifications.unread-count") }}')
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector('.notification-badge');
                if (badge && data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'flex';
                }
            })
            .catch(error => console.error('Error loading count:', error));
    }
});
</script>