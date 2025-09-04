@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="header">
        <h1>إدارة المستخدمين</h1>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i data-lucide="plus"></i>
            إضافة مستخدم
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

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
@endsection