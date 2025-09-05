<script>
let syncInterval;
let lastPlayerId = null;

function syncPlayerId() {
    const playerId = localStorage.getItem('player_id');
    
    if (playerId && playerId !== lastPlayerId) {
        fetch('{{ route("user.update-player-id") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                player_id: playerId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                lastPlayerId = playerId;
                console.log('تم تحديث معرف الإشعارات بنجاح');
            }
        })
        .catch(error => {
            console.error('خطأ في تحديث معرف الإشعارات:', error);
        });
    }
}

function startPeriodicSync() {
    syncPlayerId();
    syncInterval = setInterval(syncPlayerId, 30000);
}

function stopPeriodicSync() {
    if (syncInterval) {
        clearInterval(syncInterval);
    }
}

document.addEventListener('DOMContentLoaded', startPeriodicSync);

window.addEventListener('storage', function(e) {
    if (e.key === 'player_id') {
        syncPlayerId();
    }
});

window.addEventListener('beforeunload', stopPeriodicSync);
</script>