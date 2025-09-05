let syncInterval;
let lastPlayerId = null;

// تحديث معرف الإشعارات من localStorage
function syncPlayerId() {
    const playerId = localStorage.getItem('player_id');
    
    if (playerId && playerId !== lastPlayerId) {
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
                lastPlayerId = playerId;
                console.log('تم تحديث معرف الإشعارات بنجاح');
            }
        })
        .catch(error => {
            console.error('خطأ في تحديث معرف الإشعارات:', error);
        });
    }
}

// بدء التحقق الدوري
function startPeriodicSync() {
    // تحقق فوري
    syncPlayerId();
    
    // تحقق كل 30 ثانية
    syncInterval = setInterval(syncPlayerId, 30000);
}

// إيقاف التحقق الدوري
function stopPeriodicSync() {
    if (syncInterval) {
        clearInterval(syncInterval);
    }
}

// تشغيل التحديث عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', startPeriodicSync);

// تشغيل التحديث عند تغيير localStorage
window.addEventListener('storage', function(e) {
    if (e.key === 'player_id') {
        syncPlayerId();
    }
});

// إيقاف التحقق عند إغلاق الصفحة
window.addEventListener('beforeunload', stopPeriodicSync);