@extends('layouts.app')

@section('title', 'الإشعارات - مطبعة ريناس')

@section('content')
<div class="page-header">
    <a href="{{ route('dashboard') }}" class="back-btn">
        <i data-lucide="arrow-right"></i>
    </a>
    <h2>الإشعارات</h2>
    <button class="mark-all-read-btn" onclick="markAllAsRead()">
        <i data-lucide="check-check"></i>
        تحديد الكل كمقروء
    </button>
</div>



<div class="notifications-container">
    @forelse($notifications as $notification)
        <div class="notification-card {{ !$notification['read'] ? 'unread' : '' }}" data-id="{{ $notification['id'] }}">
            <div class="notification-icon {{ $notification['type'] }}">
                <i data-lucide="{{ $notification['icon'] }}"></i>
            </div>
            <div class="notification-content">
                <h4>{{ $notification['title'] }}</h4>
                <p>{{ $notification['message'] }}</p>
                <span class="notification-time">{{ $notification['time'] }}</span>
            </div>
            @if(!$notification['read'])
                <button class="mark-read-btn" onclick="markAsRead({{ $notification['id'] }})">
                    <i data-lucide="check"></i>
                </button>
            @endif
        </div>
    @empty
        <div class="empty-state">
            <i data-lucide="bell-off"></i>
            <h3>لا توجد إشعارات</h3>
            <p>ستظهر الإشعارات هنا عند وجودها</p>
        </div>
    @endforelse
</div>

@push('styles')
<style>
.page-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
}

.mark-all-read-btn {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-right: auto;
}

.notifications-container {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.notification-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    display: flex;
    align-items: flex-start;
    gap: 16px;
    transition: all 0.2s;
    position: relative;
}

.notification-card.unread {
    background: #fef3f2;
    border-color: #fecaca;
    border-right: 4px solid #ef4444;
}

.notification-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.notification-icon.new_order {
    background: #dbeafe;
    color: #3b82f6;
}

.notification-icon.delivery_reminder {
    background: #fef3c7;
    color: #f59e0b;
}

.notification-icon.order_completed {
    background: #dcfce7;
    color: #22c55e;
}

.notification-icon.payment_received {
    background: #f3e8ff;
    color: #8b5cf6;
}

.notification-content {
    flex: 1;
}

.notification-content h4 {
    margin: 0 0 4px 0;
    font-size: 16px;
    font-weight: 600;
    color: #111827;
}

.notification-content p {
    margin: 0 0 8px 0;
    color: #6b7280;
    line-height: 1.5;
}

.notification-time {
    font-size: 12px;
    color: #9ca3af;
}

.mark-read-btn {
    background: #f3f4f6;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #6b7280;
    transition: all 0.2s;
}

.mark-read-btn:hover {
    background: #22c55e;
    color: white;
}

.empty-state {
    text-align: center;
    padding: 48px 24px;
    color: #6b7280;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    opacity: 0.5;
}

.empty-state h3 {
    margin: 0 0 8px 0;
    font-size: 18px;
}

.empty-state p {
    margin: 0;
    font-size: 14px;
}
</style>
@endpush

@push('scripts')
<script>
function markAsRead(id) {
    fetch('{{ route("notifications.read", ":id") }}'.replace(':id', id), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const card = document.querySelector(`[data-id="${id}"]`);
            card.classList.remove('unread');
            card.querySelector('.mark-read-btn').remove();
        }
    });
}

function markAllAsRead() {
    fetch('{{ route("notifications.mark-all-read") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelectorAll('.notification-card.unread').forEach(card => {
                card.classList.remove('unread');
                const btn = card.querySelector('.mark-read-btn');
                if (btn) btn.remove();
            });
        }
    });
}
</script>
@endpush
@endsection