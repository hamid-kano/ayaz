@props(['discounts' => collect(), 'editable' => true, 'locked' => false])

@php
    // في حال فشل التحقق نعيد الخصومات التي أدخلها المستخدم
    $initialDiscounts = old('discounts') !== null
        ? collect(old('discounts'))->values()
        : collect($discounts)->map(fn($d) => [
            'id' => is_object($d) ? $d->id : ($d['id'] ?? null),
            'amount' => (float) (is_object($d) ? $d->amount : $d['amount']),
            'currency' => is_object($d) ? $d->currency : $d['currency'],
            'reason' => is_object($d) ? $d->reason : $d['reason'],
        ])->values();
    $canEdit = $editable && !$locked;
@endphp

<div class="order-items-section order-discounts-section">
    <div class="section-header">
        <i data-lucide="badge-percent"></i>
        <h3>الخصومات</h3>
    </div>

    @if($locked && $editable)
        <div class="discounts-locked-note">
            <i data-lucide="lock"></i>
            لا يمكن تعديل الخصومات على طلبية مؤرشفة أو ملغاة
        </div>
    @endif

    @if($errors->has('discounts') || $errors->has('discounts.*'))
        <div class="discounts-errors">
            @foreach(array_merge($errors->get('discounts'), ...array_values($errors->get('discounts.*'))) as $message)
                <span class="error-message">{{ $message }}</span>
            @endforeach
        </div>
    @endif

    <div class="items-list" id="discountsList">
        @forelse($discounts as $discount)
            @if(!$canEdit)
                <div class="item-card discount-card">
                    <div class="item-content">
                        <div class="item-header">
                            <div class="item-name">{{ $discount->reason }}</div>
                            <div class="item-total discount-amount">- {{ \App\Helpers\TranslationHelper::formatAmount($discount->amount) }} {{ $discount->currency == 'usd' ? 'دولار' : 'ليرة' }}</div>
                        </div>
                        @if($discount->relationLoaded('creator'))
                            <div class="item-details">
                                <span>{{ $discount->creator?->name ?? 'غير معروف' }}</span>
                                <span>{{ \App\Helpers\TranslationHelper::formatDateTime($discount->created_at, 'd/m/Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @empty
            @if(!$canEdit)
                <div class="empty-items">
                    <h4>لا توجد خصومات</h4>
                </div>
            @endif
        @endforelse
    </div>

    @if($canEdit)
        <div class="add-item-form" id="discountForm" style="display: none;">
            <div class="form-row">
                <div class="form-group">
                    <label>مبلغ الخصم</label>
                    <input type="number" id="discountAmount" placeholder="0" min="0" step="0.000001">
                </div>
                <div class="form-group">
                    <label>العملة</label>
                    <select id="discountCurrency">
                        <option value="syp">ليرة سورية</option>
                        <option value="usd">دولار أمريكي</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>سبب الخصم</label>
                    <input type="text" id="discountReason" maxlength="255" placeholder="مثال: زبون دائم">
                </div>
            </div>
            <span class="error-message" id="discountFormError" hidden></span>

            <div class="form-actions">
                <button type="button" onclick="cancelDiscountForm()" class="btn-secondary">
                    <i data-lucide="x"></i>
                    إلغاء
                </button>
                <button type="button" onclick="saveDiscount()" class="btn-primary">
                    <i data-lucide="save"></i>
                    <span id="discountSubmitText">إضافة</span>
                </button>
            </div>
        </div>

        <button type="button" onclick="showDiscountForm()" class="btn-primary" id="addDiscountBtn">
            <i data-lucide="plus"></i>
            إضافة خصم
        </button>

        <div id="discountsHiddenInputs">
            <input type="hidden" name="discounts_submitted" value="1">
        </div>

        <div class="discounts-summary" id="discountsSummary"></div>
    @endif
</div>

<style>
.discount-amount { color: #dc2626; }
.discounts-locked-note {
    display: flex; align-items: center; gap: 8px;
    padding: 12px 16px; margin-bottom: 12px;
    background: #fef3c7; color: #92400e; border-radius: 12px; font-size: 14px;
}
.discounts-locked-note i { width: 16px; height: 16px; }
.discounts-errors { display: flex; flex-direction: column; gap: 4px; margin-bottom: 12px; }
.discounts-summary { margin-top: 16px; display: grid; gap: 8px; }
.discounts-summary:empty { display: none; }
.discounts-summary .summary-row {
    display: flex; justify-content: space-between; flex-wrap: wrap; gap: 8px;
    padding: 10px 16px; border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0; font-size: 14px;
}
.discounts-summary .summary-row.net { background: #ecfdf5; border-color: #a7f3d0; font-weight: 700; }
.discounts-summary .summary-row.invalid { background: #fef2f2; border-color: #fecaca; color: #b91c1c; }
</style>

@if($canEdit)
<script>
let formDiscounts = @json($initialDiscounts);
let editingDiscountIndex = -1;

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text ?? '';
    return div.innerHTML;
}

function discountFormatAmount(amount) {
    amount = parseFloat(amount) || 0;
    if (Math.floor(amount) == amount) {
        return Math.floor(amount).toLocaleString();
    }
    return amount.toFixed(6).replace(/\.?0+$/, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function currencyLabel(currency) {
    return currency == 'usd' ? 'دولار' : 'ليرة';
}

function showDiscountForm() {
    document.getElementById('discountForm').style.display = 'block';
    document.getElementById('addDiscountBtn').style.display = 'none';
    document.getElementById('discountSubmitText').textContent = 'إضافة';
    clearDiscountForm();
    editingDiscountIndex = -1;
}

function editDiscount(index) {
    const discount = formDiscounts[index];
    showDiscountForm();
    document.getElementById('discountSubmitText').textContent = 'تحديث';
    document.getElementById('discountAmount').value = discount.amount;
    document.getElementById('discountCurrency').value = discount.currency;
    document.getElementById('discountReason').value = discount.reason;
    editingDiscountIndex = index;
}

function showDiscountError(message) {
    const el = document.getElementById('discountFormError');
    el.textContent = message;
    el.hidden = !message;
}

function saveDiscount() {
    const amountInput = document.getElementById('discountAmount');
    const currency = document.getElementById('discountCurrency').value;
    const reasonInput = document.getElementById('discountReason');
    const amount = parseFloat(amountInput.value);

    if (!amount || amount <= 0) {
        showDiscountError('أدخل مبلغ خصم أكبر من صفر');
        amountInput.focus();
        return;
    }
    if (!reasonInput.value.trim()) {
        showDiscountError('سبب الخصم مطلوب');
        reasonInput.focus();
        return;
    }

    // الخصم لا يتجاوز مجموع الطلبية بهذه العملة
    const totals = orderSubtotals();
    const otherDiscounts = formDiscounts
        .filter((d, i) => i !== editingDiscountIndex && d.currency === currency)
        .reduce((sum, d) => sum + parseFloat(d.amount), 0);
    if (totals[currency] <= 0) {
        showDiscountError('لا توجد مواد بهذه العملة حتى يُطبَّق عليها خصم');
        return;
    }
    if (otherDiscounts + amount > totals[currency] + 0.000001) {
        showDiscountError('مجموع الخصومات بهذه العملة أكبر من مجموع الطلبية');
        return;
    }

    const discount = {
        id: editingDiscountIndex >= 0 ? formDiscounts[editingDiscountIndex].id : null,
        amount: amount,
        currency: currency,
        reason: reasonInput.value.trim()
    };

    if (editingDiscountIndex >= 0) {
        formDiscounts[editingDiscountIndex] = discount;
    } else {
        formDiscounts.push(discount);
    }

    renderDiscounts();
    cancelDiscountForm();
}

function removeDiscount(index) {
    if (confirm('هل أنت متأكد من حذف هذا الخصم؟')) {
        formDiscounts.splice(index, 1);
        renderDiscounts();
    }
}

function cancelDiscountForm() {
    document.getElementById('discountForm').style.display = 'none';
    document.getElementById('addDiscountBtn').style.display = 'block';
    clearDiscountForm();
    editingDiscountIndex = -1;
}

function clearDiscountForm() {
    document.getElementById('discountAmount').value = '';
    document.getElementById('discountCurrency').value = 'syp';
    document.getElementById('discountReason').value = '';
    showDiscountError('');
}

// مجموع المواد لكل عملة من مكوّن المواد
function orderSubtotals() {
    const items = (typeof formItems !== 'undefined') ? formItems : [];
    const totals = { syp: 0, usd: 0 };
    items.forEach(item => {
        totals[item.currency] = (totals[item.currency] || 0) + item.quantity * item.price;
    });
    return totals;
}

function renderDiscounts() {
    const container = document.getElementById('discountsList');
    container.innerHTML = formDiscounts.length === 0
        ? '<div class="empty-items"><h4>لا توجد خصومات</h4><p>يمكنك إضافة خصم بمبلغ ثابت مع ذكر السبب</p></div>'
        : formDiscounts.map((d, index) => `
            <div class="item-card discount-card">
                <div class="item-content">
                    <div class="item-header">
                        <div class="item-name">${escapeHtml(d.reason)}</div>
                        <div class="item-total discount-amount">- ${discountFormatAmount(d.amount)} ${currencyLabel(d.currency)}</div>
                    </div>
                    <div class="item-actions">
                        <button type="button" class="action-btn edit" onclick="editDiscount(${index})" title="تعديل">
                            <i data-lucide="edit-2"></i>
                        </button>
                        <button type="button" class="action-btn delete" onclick="removeDiscount(${index})" title="حذف">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');

    // الحقول المخفية التي تُرسل مع النموذج
    const hidden = document.getElementById('discountsHiddenInputs');
    hidden.querySelectorAll('input[name^="discounts["]').forEach(input => input.remove());
    formDiscounts.forEach((d, index) => {
        ['id', 'amount', 'currency', 'reason'].forEach(field => {
            if (field === 'id' && !d.id) return;
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `discounts[${index}][${field}]`;
            input.value = d[field];
            hidden.appendChild(input);
        });
    });

    renderDiscountsSummary();
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

// ملخص المجموع والخصم والصافي لكل عملة
function renderDiscountsSummary() {
    const summary = document.getElementById('discountsSummary');
    const totals = orderSubtotals();
    const rows = [];

    ['syp', 'usd'].forEach(currency => {
        const discount = formDiscounts
            .filter(d => d.currency === currency)
            .reduce((sum, d) => sum + parseFloat(d.amount), 0);
        if (totals[currency] <= 0 && discount <= 0) return;

        const net = totals[currency] - discount;
        const label = currencyLabel(currency);
        rows.push(`<div class="summary-row"><span>المجموع (${label})</span><span>${discountFormatAmount(totals[currency])}</span></div>`);
        if (discount > 0) {
            rows.push(`<div class="summary-row"><span>الخصم (${label})</span><span class="discount-amount">- ${discountFormatAmount(discount)}</span></div>`);
        }
        rows.push(`<div class="summary-row net ${net < 0 ? 'invalid' : ''}"><span>الصافي (${label})</span><span>${discountFormatAmount(net)}</span></div>`);
    });

    summary.innerHTML = rows.join('');
}

document.addEventListener('order-items-changed', renderDiscountsSummary);
document.addEventListener('DOMContentLoaded', renderDiscounts);
</script>
@endif
