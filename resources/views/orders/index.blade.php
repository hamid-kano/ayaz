@extends('layouts.app')

@section('title', 'الطلبات - مطبعة ريناس')

@section('content')
    <div class="page-header">
        <a href="{{ route('dashboard') }}" class="back-btn">
            <i data-lucide="arrow-right"></i>
        </a>
        <h2>الطلبات</h2>
        @if (auth()->user()->isAdmin())
            <a href="{{ route('orders.create') }}" class="add-btn">
                <i data-lucide="plus"></i>
            </a>
        @else
            <div></div>
        @endif
    </div>



    <!-- Search Section -->
    <div class="search-container">
        <form method="GET" action="{{ route('orders.index') }}">
            <div class="search-group">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث في الطلبات...">
                <button type="submit" class="search-btn">
                    <i data-lucide="search"></i>
                </button>
            </div>
            @if (request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
        </form>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <button class="tab-btn {{ request('status') == '' ? 'active' : '' }}" onclick="filterOrders('')">
            الكل <span class="count">{{ $statusCounts['all'] }}</span>
        </button>
        <button class="tab-btn {{ request('status') == 'new' ? 'active' : '' }}" onclick="filterOrders('new')">
            جديدة <span class="count">{{ $statusCounts['new'] }}</span>
        </button>
        <button class="tab-btn {{ request('status') == 'in-progress' ? 'active' : '' }}"
            onclick="filterOrders('in-progress')">
            قيد التنفيذ <span class="count">{{ $statusCounts['in-progress'] }}</span>
        </button>
        <button class="tab-btn {{ request('status') == 'ready' ? 'active' : '' }}" onclick="filterOrders('ready')">
            جاهزة <span class="count">{{ $statusCounts['ready'] }}</span>
        </button>
        <button class="tab-btn {{ request('status') == 'delivered' ? 'active' : '' }}" onclick="filterOrders('delivered')">
            تم التسليم <span class="count">{{ $statusCounts['delivered'] }}</span>
        </button>
        <button class="tab-btn {{ request('status') == 'archived' ? 'active' : '' }}" onclick="filterOrders('archived')">
            مؤرشفة <span class="count">{{ $statusCounts['archived'] }}</span>
        </button>
        <button class="tab-btn {{ request('status') == 'cancelled' ? 'active' : '' }}" onclick="filterOrders('cancelled')">
            ملغاة <span class="count">{{ $statusCounts['cancelled'] }}</span>
        </button>
    </div>

    <!-- Orders Grid -->
    <div class="orders-grid">
        @forelse($orders as $order)
            <div class="order-card" data-order="{{ $order->order_number }}" data-customer="{{ $order->customer_name }}"
                data-type="{{ $order->order_type }}" data-urgent="{{ $order->is_urgent ? 'true' : 'false' }}">
                <div class="order-header">
                    <div class="order-number">
                        #{{ $order->order_number }}
                        @if ($order->is_urgent)
                            <span class="urgent-badge">مستعجلة</span>
                        @endif
                    </div>
                    <div class="order-status {{ $order->status }}">
                        @switch($order->status)
                            @case('new')
                                جديدة
                            @break

                            @case('in-progress')
                                قيد التنفيذ
                            @break

                            @case('ready')
                                جاهزة
                            @break

                            @case('delivered')
                                تم التسليم
                            @break

                            @case('archived')
                                مؤرشفة
                            @break

                            @case('cancelled')
                                ملغاة
                            @break
                        @endswitch
                    </div>
                </div>
                <div class="order-info">
                    <h4>{{ $order->customer_name }}</h4>
                    @if ($order->customer_phone)
                        <p class="order-phone">{{ $order->customer_phone }}</p>
                    @endif
                    <p class="order-type">{{ $order->order_type }}</p>
                    <p class="order-details">{{ Str::limit($order->order_details, 50) }}</p>
                </div>
                <div class="order-footer">
                    <div class="order-meta">
                        <div class="order-cost">
                            @if ($order->total_cost_syp > 0 && $order->total_cost_usd > 0)
                                {{ \App\Helpers\TranslationHelper::formatAmount($order->total_cost_syp) }} ل.س +
                                {{ \App\Helpers\TranslationHelper::formatAmount($order->total_cost_usd) }} $
                            @elseif($order->total_cost_syp > 0)
                                {{ \App\Helpers\TranslationHelper::formatAmount($order->total_cost_syp) }} ل.س
                            @elseif($order->total_cost_usd > 0)
                                {{ \App\Helpers\TranslationHelper::formatAmount($order->total_cost_usd) }} $
                            @else
                                0 ل.س
                            @endif
                        </div>
                        <div class="order-date">{{ $order->order_date->format('Y-m-d') }}</div>
                    </div>
                </div>
                <div class="order-actions">
                    <a href="{{ route('orders.show', $order) }}" class="action-btn details" title="عرض التفاصيل">
                        <i data-lucide="eye"></i>
                    </a>
                    <a href="{{ route('orders.public-print', $order) }}" class="action-btn print" title="طباعة"
                        target="_blank">
                        <i data-lucide="printer"></i>
                    </a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('receipts.index', ['order_id' => $order->id]) }}" class="action-btn receipts"
                            title="المقبوضات">
                            <i data-lucide="credit-card"></i>
                        </a>
                        <a href="{{ route('orders.edit', $order) }}" class="action-btn edit" title="تعديل">
                            <i data-lucide="edit-2"></i>
                        </a>
                        @if ($order->canBeArchived())
                            <form method="POST" action="{{ route('orders.archive', $order) }}" style="display: inline;">
                                @csrf
                                <button type="button" class="action-btn archive" title="أرشفة"
                                    onclick="showArchiveModal('{{ route('orders.archive', $order) }}', 'الطلبية #{{ $order->order_number }}', this.closest('form'))">
                                    <i data-lucide="archive"></i>
                                </button>
                            </form>
                        @endif
                        @if ($order->remaining_amount_syp <= 0 && $order->remaining_amount_usd <= 0)
                            <form method="POST" action="{{ route('orders.destroy', $order) }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="action-btn delete" title="حذف"
                                    onclick="showDeleteModal('{{ route('orders.destroy', $order) }}', 'الطلبية #{{ $order->order_number }}', this.closest('form'))">
                                    <i data-lucide="trash-2"></i>
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
            @empty
                <div class="empty-state">
                    <i data-lucide="file-text"></i>
                    <h3>لا توجد طلبيات</h3>
                    @if (auth()->user()->isAdmin())
                        <p>ابدأ بإضافة طلبية جديدة</p>
                        <a href="{{ route('orders.create') }}" class="btn-primary">
                            <i data-lucide="plus"></i>
                            إضافة طلبية
                        </a>
                    @elseif (auth()->user()->isAuditor())
                        <p>لا توجد طلبيات للعرض</p>
                    @else
                        <p>لم يتم تعيين أي طلبيات لك بعد</p>
                    @endif
                </div>
            @endforelse
        </div>



        @push('scripts')
            <script>
                function filterOrders(status) {
                    const url = new URL(window.location);
                    if (status) {
                        url.searchParams.set('status', status);
                    } else {
                        url.searchParams.delete('status');
                    }
                    window.location = url;
                }

                function showArchiveModal(archiveUrl, itemName, formElement) {
                    const modal = document.querySelector('[x-data*="open: false"]');
                    if (modal) {
                        const message = modal.querySelector('p');
                        if (message) {
                            message.textContent = `هل أنت متأكد من أنك تريد أرشفة ${itemName}؟ سيتم نقلها إلى الأرشيف.`;
                        }
                        const title = modal.querySelector('h3');
                        if (title) {
                            title.textContent = 'تأكيد الأرشفة';
                        }
                    }

                    window.dispatchEvent(new CustomEvent('delete-modal'));

                    const handleConfirm = (e) => {
                        if (formElement) {
                            formElement.submit();
                        }
                        window.removeEventListener('confirm-delete', handleConfirm);
                    };

                    window.addEventListener('confirm-delete', handleConfirm);
                }
            </script>
        @endpush
    @endsection
