@extends('layouts.app')

@section('title', 'التقارير - مطبعة ريناس')

@section('content')
<div class="page-header">
    <a href="{{ route('dashboard') }}" class="back-btn">
        <i data-lucide="arrow-right"></i>
    </a>
    <h2>التقارير</h2>
    <button class="export-btn">
        <i data-lucide="download"></i>
    </button>
</div>

<!-- Filters Section -->
<form method="GET" action="{{ route('reports.index') }}" class="search-container">
    <div class="search-group">
        <select name="period" class="filter-select" onchange="this.form.submit()">
            <option value="30" {{ request('period') == '30' ? 'selected' : '' }}>آخر 30 يوم</option>
            <option value="90" {{ request('period') == '90' ? 'selected' : '' }}>آخر 3 أشهر</option>
            <option value="180" {{ request('period') == '180' ? 'selected' : '' }}>آخر 6 أشهر</option>
            <option value="365" {{ request('period') == '365' ? 'selected' : '' }}>السنة الحالية</option>
        </select>
    </div>
</form>



<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card revenue">
        <div class="stat-icon">
            <i data-lucide="trending-up"></i>
        </div>
        <div class="stat-content">
            <h3>{{ number_format($stats['total_revenue']) }} ل.س</h3>
            <p>إجمالي الإيرادات</p>
            <span class="stat-change positive">+12%</span>
        </div>
    </div>

    <div class="stat-card expenses">
        <div class="stat-icon">
            <i data-lucide="trending-down"></i>
        </div>
        <div class="stat-content">
            <h3>{{ number_format($stats['total_expenses']) }} ل.س</h3>
            <p>إجمالي المصروفات</p>
            <span class="stat-change negative">+5%</span>
        </div>
    </div>

    <div class="stat-card profit">
        <div class="stat-icon">
            <i data-lucide="dollar-sign"></i>
        </div>
        <div class="stat-content">
            <h3>{{ number_format($stats['net_profit']) }} ل.س</h3>
            <p>صافي الربح</p>
            <span class="stat-change positive">+18%</span>
        </div>
    </div>

    <div class="stat-card orders">
        <div class="stat-icon">
            <i data-lucide="package"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['total_orders'] }}</h3>
            <p>إجمالي الطلبات</p>
            <span class="stat-change positive">+8%</span>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="charts-section">
    <div class="chart-card">
        <div class="chart-header">
            <h3>الإيرادات والمصروفات الشهرية</h3>
        </div>
        <div class="chart-container">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-header">
            <h3>توزيع الطلبات</h3>
        </div>
        <div class="chart-container">
            <canvas id="ordersChart"></canvas>
        </div>
    </div>
</div>

<!-- Tables Section -->
<div class="tables-section">
    <div class="table-card">
        <div class="table-header">
            <h3>أفضل العملاء</h3>
        </div>
        <div class="table-container">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>اسم العميل</th>
                        <th>عدد الطلبات</th>
                        <th>إجمالي المبلغ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topCustomers as $customer)
                    <tr>
                        <td>{{ $customer['name'] }}</td>
                        <td>{{ $customer['orders'] }}</td>
                        <td>{{ number_format($customer['total']) }} ل.س</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="debt-card">
        <div class="debt-header">
            <div class="debt-title">
                <i data-lucide="credit-card"></i>
                <h3>الديون والمستحقات</h3>
            </div>
        </div>
        <div class="debt-content">
            <div class="debt-section">
                <div class="debt-type-header">
                    <i data-lucide="arrow-up-circle" class="debt-icon debt-owed"></i>
                    <span class="debt-type-title">ديون علينا</span>
                </div>
                <div class="debt-amounts">
                    <div class="debt-amount-item">
                        <span class="amount debt-on-us">{{ number_format($stats['debts_on_us_syp']) }}</span>
                        <span class="currency">ليرة سورية</span>
                    </div>
                    <div class="debt-amount-item">
                        <span class="amount debt-on-us">{{ number_format($stats['debts_on_us_usd']) }}</span>
                        <span class="currency">دولار أمريكي</span>
                    </div>
                </div>
            </div>
            
            <div class="debt-divider"></div>
            
            <div class="debt-section">
                <div class="debt-type-header">
                    <i data-lucide="arrow-down-circle" class="debt-icon debt-receivable"></i>
                    <span class="debt-type-title">ديون لنا</span>
                </div>
                <div class="debt-amounts">
                    <div class="debt-amount-item">
                        <span class="amount debt-for-us">{{ number_format($stats['outstanding_debts_syp']) }}</span>
                        <span class="currency">ليرة سورية</span>
                    </div>
                    <div class="debt-amount-item">
                        <span class="amount debt-for-us">{{ number_format($stats['outstanding_debts_usd']) }}</span>
                        <span class="currency">دولار أمريكي</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.page-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
}

.back-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: #374151;
    transition: all 0.2s;
}

.back-btn:hover {
    background: #e5e7eb;
    color: #111827;
}

.page-header h2 {
    flex: 1;
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: #111827;
}

.export-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #10b981;
    color: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.export-btn:hover {
    background: #059669;
}

.search-container {
    margin-bottom: 24px;
}

.search-group {
    display: flex;
    gap: 8px;
    align-items: center;
}

.filter-select {
    flex: 1;
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 12px;
    background: white;
    font-size: 16px;
    color: #374151;
}

.filter-btn {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #3b82f6;
    color: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.filter-btn:hover {
    background: #2563eb;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.stat-card.revenue .stat-icon {
    background: #dbeafe;
    color: #3b82f6;
}

.stat-card.expenses .stat-icon {
    background: #fecaca;
    color: #ef4444;
}

.stat-card.profit .stat-icon {
    background: #dcfce7;
    color: #22c55e;
}

.stat-card.orders .stat-icon {
    background: #fef3c7;
    color: #f59e0b;
}

.stat-content h3 {
    margin: 0 0 4px 0;
    font-size: 24px;
    font-weight: 700;
    color: #111827;
}

.stat-content p {
    margin: 0 0 8px 0;
    color: #6b7280;
    font-size: 14px;
}

.stat-change {
    font-size: 12px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 12px;
}

.stat-change.positive {
    background: #dcfce7;
    color: #22c55e;
}

.stat-change.negative {
    background: #fecaca;
    color: #ef4444;
}

.charts-section {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
    margin-bottom: 32px;
}

.chart-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.chart-header h3 {
    margin: 0 0 20px 0;
    color: #111827;
    font-size: 18px;
}

.chart-container {
    height: 300px;
    position: relative;
}

.tables-section {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
}

.table-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.table-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}

.table-header h3 {
    margin: 0;
    color: #111827;
    font-size: 16px;
}

.report-table {
    width: 100%;
    border-collapse: collapse;
}

.report-table th,
.report-table td {
    padding: 12px 24px;
    text-align: right;
    border-bottom: 1px solid #f3f4f6;
}

.report-table th {
    background: #f9fafb;
    font-weight: 600;
    color: #374151;
    font-size: 14px;
}

.debt-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.debt-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.debt-title {
    display: flex;
    align-items: center;
    gap: 12px;
}

.debt-title i {
    color: #3b82f6;
    font-size: 20px;
}

.debt-title h3 {
    margin: 0;
    color: #111827;
    font-size: 16px;
    font-weight: 600;
}

.debt-content {
    padding: 24px;
}

.debt-section {
    margin-bottom: 24px;
}

.debt-section:last-child {
    margin-bottom: 0;
}

.debt-type-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}

.debt-icon {
    font-size: 18px;
}

.debt-icon.debt-owed {
    color: #ef4444;
}

.debt-icon.debt-receivable {
    color: #22c55e;
}

.debt-type-title {
    font-weight: 600;
    color: #374151;
    font-size: 15px;
}

.debt-amounts {
    display: grid;
    gap: 12px;
}

.debt-amount-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: #f9fafb;
    border-radius: 8px;
    border-left: 4px solid transparent;
}

.debt-amount-item .amount {
    font-weight: 700;
    font-size: 16px;
}

.debt-amount-item .currency {
    color: #6b7280;
    font-size: 13px;
    font-weight: 500;
}

.debt-on-us {
    color: #ef4444;
}

.debt-for-us {
    color: #22c55e;
}

.debt-amount-item:has(.debt-on-us) {
    border-left-color: #ef4444;
    background: #fef2f2;
}

.debt-amount-item:has(.debt-for-us) {
    border-left-color: #22c55e;
    background: #f0fdf4;
}

.debt-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent 0%, #e5e7eb 50%, transparent 100%);
    margin: 24px 0;
}

@media (max-width: 1024px) {
    .charts-section {
        grid-template-columns: 1fr;
    }
    
    .tables-section {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .page-header h2 {
        font-size: 20px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .stat-card {
        padding: 16px;
    }
    
    .stat-content h3 {
        font-size: 20px;
    }
    
    .chart-card {
        padding: 16px;
    }
    
    .chart-container {
        height: 250px;
    }
    
    .report-table th,
    .report-table td {
        padding: 8px 12px;
        font-size: 14px;
    }
    
    .debt-summary {
        padding: 16px;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        gap: 12px;
    }
    
    .stat-card {
        flex-direction: column;
        text-align: center;
        padding: 12px;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        font-size: 20px;
    }
    
    .stat-content h3 {
        font-size: 18px;
    }
    
    .chart-container {
        height: 200px;
    }
    
    .report-table {
        font-size: 12px;
    }
    
    .report-table th,
    .report-table td {
        padding: 6px 8px;
    }
    
    .debt-content {
        padding: 16px;
    }
    
    .debt-amount-item {
        padding: 10px 12px;
    }
    
    .debt-amount-item .amount {
        font-size: 14px;
    }
    
    .search-group {
        gap: 12px;
    }
    
    .filter-select {
        font-size: 14px;
        padding: 10px 12px;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// تطبيق الخط العربي على المخططات
Chart.defaults.font.family = 'Cairo, Tajawal, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
Chart.defaults.font.size = 12;

// Monthly Revenue/Expenses Chart
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($monthlyData, 'month')) !!},
        datasets: [{
            label: 'الإيرادات',
            data: {!! json_encode(array_column($monthlyData, 'revenue')) !!},
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4
        }, {
            label: 'المصروفات',
            data: {!! json_encode(array_column($monthlyData, 'expenses')) !!},
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    font: {
                        family: 'Cairo, Tajawal, sans-serif',
                        size: 13
                    }
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    font: {
                        family: 'Cairo, Tajawal, sans-serif',
                        size: 11
                    }
                }
            },
            y: {
                ticks: {
                    font: {
                        family: 'Cairo, Tajawal, sans-serif',
                        size: 11
                    }
                }
            }
        }
    }
});

// Orders Distribution Chart
const ordersCtx = document.getElementById('ordersChart').getContext('2d');
new Chart(ordersCtx, {
    type: 'doughnut',
    data: {
        labels: ['مكتملة', 'قيد التنفيذ', 'ملغاة'],
        datasets: [{
            data: [{{ $stats['completed_orders'] }}, {{ $stats['pending_orders'] }}, 5],
            backgroundColor: ['#22c55e', '#f59e0b', '#ef4444']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    font: {
                        family: 'Cairo, Tajawal, sans-serif',
                        size: 12
                    }
                }
            }
        }
    }
});
</script>
@endpush
@endsection