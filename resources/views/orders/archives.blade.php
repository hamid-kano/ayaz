@extends('layouts.app')

@section('title', 'أرشيف الطلبيات - مطبعة ريناس')

@section('content')
    <div class="page-header">
        <a href="{{ route('orders.index') }}" class="back-btn">
            <i data-lucide="arrow-right"></i>
        </a>
        <h2>أرشيف الطلبيات</h2>
        <div></div>
    </div>

    <!-- Search Section -->
    <div class="search-container">
        <form method="GET" action="{{ route('orders.archives') }}">
            <div class="search-group">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث في الأرشيف...">
                <button type="submit" class="search-btn">
                    <i data-lucide="search"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Archive Folders -->
    <div class="archive-folders">
        @forelse($archives as $folder => $orders)
            <div class="archive-folder">
                <div class="folder-header">
                    <div class="folder-info">
                        <i data-lucide="folder"></i>
                        <h3>{{ \Carbon\Carbon::createFromFormat('Y-m', $folder)->locale('ar')->translatedFormat('F Y') }}</h3>
                        <span class="folder-count">{{ $orders->count() }} طلبية</span>
                    </div>
                    <button class="folder-toggle" onclick="toggleFolder('{{ $folder }}')">
                        <i data-lucide="chevron-down"></i>
                    </button>
                </div>
                
                <div class="folder-content" id="folder-{{ $folder }}">
                    <div class="orders-grid">
                        @foreach($orders as $order)
                            <div class="order-card archived">
                                <div class="order-header">
                                    <div class="order-number">
                                        #{{ $order->order_number }}
                                        @if($order->is_urgent)
                                            <span class="urgent-badge">مستعجلة</span>
                                        @endif
                                    </div>
                                    <div class="order-status archived">
                                        مؤرشفة
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
                                            @if($order->total_cost_syp > 0 && $order->total_cost_usd > 0)
                                                {{ \App\Helpers\TranslationHelper::formatAmount($order->total_cost_syp) }} ل.س + {{ \App\Helpers\TranslationHelper::formatAmount($order->total_cost_usd) }} $
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
                                    <a href="{{ route('orders.public-print', $order) }}" class="action-btn print" title="طباعة" target="_blank">
                                        <i data-lucide="printer"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i data-lucide="archive"></i>
                <h3>لا توجد طلبيات مؤرشفة</h3>
                <p>لم يتم أرشفة أي طلبيات بعد</p>
            </div>
        @endforelse
    </div>

    @push('scripts')
        <script>
            function toggleFolder(folder) {
                const content = document.getElementById('folder-' + folder);
                const toggle = content.previousElementSibling.querySelector('.folder-toggle i');
                
                if (content.style.display === 'none' || content.style.display === '') {
                    content.style.display = 'block';
                    toggle.style.transform = 'rotate(180deg)';
                } else {
                    content.style.display = 'none';
                    toggle.style.transform = 'rotate(0deg)';
                }
            }
        </script>
    @endpush

    @push('styles')
        <style>
            .archive-folders {
                padding: 0 20px;
            }

            .archive-folder {
                background: white;
                border-radius: 15px;
                margin-bottom: 20px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.08);
                overflow: hidden;
            }

            .folder-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 20px;
                background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
                color: white;
                cursor: pointer;
            }

            .folder-info {
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .folder-info i {
                font-size: 24px;
            }

            .folder-info h3 {
                margin: 0;
                font-size: 18px;
                font-weight: 600;
            }

            .folder-count {
                background: rgba(255,255,255,0.2);
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
            }

            .folder-toggle {
                background: none;
                border: none;
                color: white;
                cursor: pointer;
                padding: 8px;
                border-radius: 50%;
                transition: all 0.3s ease;
            }

            .folder-toggle:hover {
                background: rgba(255,255,255,0.1);
            }

            .folder-toggle i {
                transition: transform 0.3s ease;
            }

            .folder-content {
                display: none;
                padding: 20px;
            }

            .order-card.archived {
                border-left: 4px solid #8b5cf6;
            }

            .order-status.archived {
                background: rgba(139, 92, 246, 0.1);
                color: #8b5cf6;
            }

            @media (max-width: 768px) {
                .archive-folders {
                    padding: 0 15px;
                }

                .folder-header {
                    padding: 15px;
                }

                .folder-info {
                    gap: 10px;
                }

                .folder-info h3 {
                    font-size: 16px;
                }

                .folder-content {
                    padding: 15px;
                }
            }
        </style>
    @endpush
@endsection