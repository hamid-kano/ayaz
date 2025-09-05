// تحديث معرف الإشعارات من localStorage
function syncPlayerId() {
    const playerId = localStorage.getItem('player_id');
    
    if (playerId) {
        fetch('/user/update-player-id', {
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
                console.log('تم تحديث معرف الإشعارات بنجاح');
            }
        })
        .catch(error => {
            console.error('خطأ في تحديث معرف الإشعارات:', error);
        });
    }
}

// تشغيل التحديث عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', syncPlayerId);

// تشغيل التحديث عند تغيير localStorage
window.addEventListener('storage', function(e) {
    if (e.key === 'player_id') {
        syncPlayerId();
    }
});