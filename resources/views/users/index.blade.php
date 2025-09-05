@extends('layouts.app')

@section('content')
<div class="page-header">
    <button class="back-btn" onclick="window.location.href='{{ route('dashboard') }}'">
        <i data-lucide="arrow-right"></i>
    </button>
    <h2>إدارة المستخدمين</h2>
    <button class="add-btn" onclick="window.location.href='{{ route('users.create') }}'">
        <i data-lucide="plus"></i>
    </button>
</div>

<div class="container-fluid">





    <div class="users-grid">
        @foreach($users as $user)
            <div class="user-card">
                <div class="user-info">
                    <h3>{{ $user->name }}</h3>
                    <p class="email">{{ $user->email }}</p>
                    <div class="user-meta">
                        <span class="role role-{{ $user->role }}">
                            {{ $user->role === 'admin' ? 'مدير' : 'مستخدم' }}
                        </span>
                        <span class="status status-{{ $user->is_active ? 'active' : 'inactive' }}">
                            {{ $user->is_active ? 'نشط' : 'معطل' }}
                        </span>
                    </div>
                    <div class="player-id-section">
                        <span class="player-id-label">معرف اللاعب:</span>
                        @if($user->player_id)
                            <span class="player-id-value" onclick="copyToClipboard('{{ $user->player_id }}')" title="انقر للنسخ">
                                {{ Str::limit($user->player_id, 8) }}
                            </span>
                        @else
                            <span class="player-id-empty">غير موجود</span>
                        @endif
                    </div>
                </div>
                <div class="user-actions">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-edit">
                        <i data-lucide="edit"></i>
                    </a>
                    @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-delete" onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                <i data-lucide="trash-2"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{ $users->links() }}
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('تم نسخ معرف اللاعب');
    });
}
</script>

<style>
.player-id-section {
    margin-top: 8px;
    font-size: 0.85em;
}
.player-id-label {
    color: #666;
    margin-left: 5px;
}
.player-id-value {
    background: #f0f0f0;
    padding: 2px 6px;
    border-radius: 4px;
    cursor: pointer;
    font-family: monospace;
}
.player-id-value:hover {
    background: #e0e0e0;
}
.player-id-empty {
    color: #999;
    font-style: italic;
}
</style>
@endsection