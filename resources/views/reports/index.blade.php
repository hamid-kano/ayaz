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
<form method="GET" action="{{ route('reports.index') }}" class="search-container" onsubmit="return submitReportFilters(this)">
    <div class="search-group">
        <select name="period" class="filter-select" onchange="submitReportFilters(this.form)">
            <option value="30" {{ request('period') == '30' ? 'selected' : '' }}>آخر 30 يوم</option>
            <option value="90" {{ request('period') == '90' ? 'selected' : '' }}>آخر 3 أشهر</option>
            <option value="180" {{ request('period') == '180' ? 'selected' : '' }}>آخر 6 أشهر</option>
            <option value="365" {{ request('period') == '365' ? 'selected' : '' }}>السنة الحالية</option>
        </select>

        <select name="executor_id" class="filter-select" onchange="submitReportFilters(this.form)">
            <option value="">كل المنفّذين</option>
            @foreach($executors as $executor)
                <option value="{{ $executor->id }}" {{ request('executor_id') == $executor->id ? 'selected' : '' }}>{{ $executor->name }}</option>
            @endforeach
        </select>

        <div class="date-range-group">
            <label class="date-range-label">
                <span>من</span>
                <input type="date" name="date_from" class="filter-select" value="{{ request('date_from') }}" onchange="submitReportFilters(this.form)">
            </label>
            <label class="date-range-label">
                <span>إلى</span>
                <input type="date" name="date_to" class="filter-select" value="{{ request('date_to') }}" onchange="submitReportFilters(this.form)">
            </label>
        </div>

        <a href="{{ route('reports.index') }}" class="reset-filters-btn" title="إعادة تعيين الفلاتر">
            <i data-lucide="rotate-ccw"></i>
            <span>إعادة تعيين</span>
        </a>
    </div>
</form>

<script>
function submitReportFilters(form) {
    form.querySelectorAll('select, input').forEach(function (field) {
        field.disabled = field.value === '';
    });
    form.submit();
    return false;
}
</script>



<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card revenue">
        <div class="stat-icon">
            <i data-lucide="trending-up"></i>
        </div>
        <div class="stat-content">
            <x-dual-currency-amount :syp="$stats['total_revenue_syp']" :usd="$stats['total_revenue_usd']" />
            <p>إجمالي الإيرادات</p>
            <span class="stat-change positive">+12%</span>
        </div>
    </div>

    <div class="stat-card expenses">
        <div class="stat-icon">
            <i data-lucide="trending-down"></i>
        </div>
        <div class="stat-content">
            <x-dual-currency-amount :syp="$stats['total_expenses_syp']" :usd="$stats['total_expenses_usd']" />
            <p>إجمالي المصروفات</p>
            <span class="stat-change negative">+5%</span>
        </div>
    </div>

    <div class="stat-card profit">
        <div class="stat-icon">
            <i data-lucide="dollar-sign"></i>
        </div>
        <div class="stat-content">
            <x-dual-currency-amount :syp="$stats['net_profit_syp']" :usd="$stats['net_profit_usd']" />
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

    <div class="stat-card cash-purchases">
        <div class="stat-icon">
            <i data-lucide="banknote"></i>
        </div>
        <div class="stat-content">
            <x-dual-currency-amount :syp="$stats['cash_purchases_syp']" :usd="$stats['cash_purchases_usd']" />
            <p>مشتريات نقداً</p>
        </div>
    </div>

    <div class="stat-card debt-purchases">
        <div class="stat-icon">
            <i data-lucide="credit-card"></i>
        </div>
        <div class="stat-content">
            <x-dual-currency-amount :syp="$stats['debt_purchases_syp']" :usd="$stats['debt_purchases_usd']" />
            <p>مشتريات بالدين</p>
        </div>
    </div>

    <div class="stat-card cash-sales">
        <div class="stat-icon">
            <i data-lucide="dollar-sign"></i>
        </div>
        <div class="stat-content">
            <x-dual-currency-amount :syp="$stats['cash_sales_syp']" :usd="$stats['cash_sales_usd']" />
            <p>مبيعات نقداً</p>
        </div>
    </div>

    <div class="stat-card debt-sales">
        <div class="stat-icon">
            <i data-lucide="trending-up"></i>
        </div>
        <div class="stat-content">
            <x-dual-currency-amount :syp="$stats['debt_sales_syp']" :usd="$stats['debt_sales_usd']" />
            <p>مبيعات بالدين</p>
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

    <div class="chart-card">
        <div class="chart-header">
            <h3>طلبيات حسب المنفّذ</h3>
        </div>
        <div class="chart-container">
            <canvas id="executorChart"></canvas>
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-header">
            <h3>مقارنة المبيعات: السنة الحالية مقابل الماضية</h3>
        </div>
        <div class="chart-container">
            <canvas id="yearComparisonChart"></canvas>
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-header">
            <h3>معدّل نمو المبيعات الشهري</h3>
        </div>
        <div class="chart-container">
            <canvas id="growthChart"></canvas>
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
                        <th>اسم الزبون</th>
                        <th>عدد الطلبات</th>
                        <th>إجمالي المبلغ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topCustomers as $customer)
                    <tr>
                        <td>{{ $customer['name'] }}</td>
                        <td>{{ $customer['orders'] }}</td>
                        <td>
                            @if($customer['total_syp'] > 0 && $customer['total_usd'] > 0)
                                {{ \App\Helpers\TranslationHelper::formatAmount($customer['total_syp']) }} ل.س + {{ \App\Helpers\TranslationHelper::formatAmount($customer['total_usd']) }} $
                            @elseif($customer['total_syp'] > 0)
                                {{ \App\Helpers\TranslationHelper::formatAmount($customer['total_syp']) }} ل.س
                            @elseif($customer['total_usd'] > 0)
                                {{ \App\Helpers\TranslationHelper::formatAmount($customer['total_usd']) }} $
                            @else
                                0
                            @endif
                        </td>
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
                        <span class="amount debt-on-us">{{ \App\Helpers\TranslationHelper::formatAmount($stats['debts_on_us_syp']) }}</span>
                        <span class="currency">ليرة سورية</span>
                    </div>
                    <div class="debt-amount-item">
                        <span class="amount debt-on-us">{{ \App\Helpers\TranslationHelper::formatAmount($stats['debts_on_us_usd']) }}</span>
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
                        <span class="amount debt-for-us">{{ \App\Helpers\TranslationHelper::formatAmount($stats['outstanding_debts_syp']) }}</span>
                        <span class="currency">ليرة سورية</span>
                    </div>
                    <div class="debt-amount-item">
                        <span class="amount debt-for-us">{{ \App\Helpers\TranslationHelper::formatAmount($stats['outstanding_debts_usd']) }}</span>
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
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}

.filter-select {
    flex: 1;
    min-width: 150px;
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 12px;
    background: white;
    font-size: 16px;
    color: #374151;
}

.date-range-group {
    display: flex;
    flex-wrap: nowrap;
    gap: 8px;
    flex: 2;
    min-width: 280px;
}

.date-range-label {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 0 12px;
    border: 1px solid #d1d5db;
    border-radius: 12px;
    background: white;
    font-size: 13px;
    color: #6b7280;
    font-weight: 600;
    white-space: nowrap;
}

.date-range-label .filter-select {
    min-width: 0;
    border: none;
    padding: 12px 0;
    flex: 1;
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
    transition: transform 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
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

.stat-card.cash-purchases .stat-icon {
    background: #d1fae5;
    color: #10b981;
}

.stat-card.debt-purchases .stat-icon {
    background: #fef2f2;
    color: #ef4444;
}

.stat-card.cash-sales .stat-icon {
    background: #e0f2fe;
    color: #0891b2;
}

.stat-card.debt-sales .stat-icon {
    background: #fdf4ff;
    color: #a855f7;
}

.stat-content h3 {
    margin: 0 0 4px 0;
    font-size: 20px;
    font-weight: 700;
    color: #111827;
    line-height: 1.3;
}

.dual-amount {
    display: flex;
    flex-direction: column;
    gap: 2px;
    margin: 0 0 4px 0;
}

.dual-amount-line {
    font-size: 20px;
    font-weight: 700;
    color: #111827;
    line-height: 1.3;
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
    .date-range-group {
        min-width: 100%;
    }

    .date-range-label {
        font-size: 12px;
        padding: 0 8px;
    }

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

    .dual-amount-line {
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
            label: 'الإيرادات (ل.س)',
            data: {!! json_encode(array_column($monthlyData, 'revenue_syp')) !!},
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4
        }, {
            label: 'الإيرادات ($)',
            data: {!! json_encode(array_column($monthlyData, 'revenue_usd')) !!},
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4
        }, {
            label: 'المصروفات (ل.س)',
            data: {!! json_encode(array_column($monthlyData, 'expenses_syp')) !!},
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.4
        }, {
            label: 'المصروفات ($)',
            data: {!! json_encode(array_column($monthlyData, 'expenses_usd')) !!},
            borderColor: '#f59e0b',
            backgroundColor: 'rgba(245, 158, 11, 0.1)',
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
            data: [{{ $stats['completed_orders'] }}, {{ $stats['pending_orders'] }}, {{ $stats['cancelled_orders'] }}],
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

// Orders by Executor Chart
const executorCtx = document.getElementById('executorChart').getContext('2d');
new Chart(executorCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($ordersByExecutor->pluck('name')) !!},
        datasets: [{
            label: 'عدد الطلبيات',
            data: {!! json_encode($ordersByExecutor->pluck('count')) !!},
            backgroundColor: '#3b82f6'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } }
        }
    }
});

// Year-over-Year Comparison Chart
const yearComparisonCtx = document.getElementById('yearComparisonChart').getContext('2d');
new Chart(yearComparisonCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_column($yearComparisonData, 'month')) !!},
        datasets: [{
            label: 'السنة الحالية (ل.س)',
            data: {!! json_encode(array_column($yearComparisonData, 'current_syp')) !!},
            backgroundColor: '#3b82f6'
        }, {
            label: 'السنة الماضية (ل.س)',
            data: {!! json_encode(array_column($yearComparisonData, 'previous_syp')) !!},
            backgroundColor: '#94a3b8'
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
        }
    }
});

// Monthly Sales Growth Rate Chart
const growthCtx = document.getElementById('growthChart').getContext('2d');
new Chart(growthCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($monthlyGrowthData, 'month')) !!},
        datasets: [{
            label: 'نسبة النمو (%)',
            data: {!! json_encode(array_column($monthlyGrowthData, 'growth')) !!},
            borderColor: '#8b5cf6',
            backgroundColor: 'rgba(139, 92, 246, 0.1)',
            tension: 0.4,
            spanGaps: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                ticks: {
                    callback: function(value) { return value + '%'; }
                }
            }
        }
    }
});
</script>
@endpush
@endsection