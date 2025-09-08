@props(['items' => [], 'editable' => true, 'orderId' => null])

<div class="order-items-section">
    <div class="section-header">
        <i data-lucide="package"></i>
        <h3>تفاصيل المواد</h3>
    </div>

    <div class="items-list" id="itemsList">
        @forelse($items as $index => $item)
            <div class="item-card" data-index="{{ $index }}">
                <div class="item-info">
                    <div class="item-name">{{ is_object($item) ? $item->item_name : $item['item_name'] }}</div>
                    <div class="item-details">
                        <span>الكمية: {{ is_object($item) ? $item->quantity : $item['quantity'] }}</span>
                        <span>السعر: {{ \App\Helpers\TranslationHelper::formatAmount(is_object($item) ? $item->price : $item['price']) }} {{ (is_object($item) ? $item->currency : $item['currency']) == 'usd' ? 'دولار' : 'ليرة' }}</span>
                    </div>
                </div>
                @if($editable)
                    <div class="item-actions">
                        <button type="button" class="action-btn edit" onclick="editFormItem({{ $index }})" title="تعديل">
                            <i data-lucide="edit-2"></i>
                        </button>
                        <button type="button" class="action-btn delete" onclick="confirmDeleteItem({{ $index }})" title="حذف">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </div>
                @endif
            </div>
        @empty
            <div class="empty-items">
                <div class="empty-icon">
                    <i data-lucide="package-x"></i>
                </div>
                <h4>لا توجد مواد مضافة</h4>
                <p>قم بإضافة مواد لهذه الطلبية</p>
            </div>
        @endforelse
    </div>

    @if($editable)
        <div class="add-item-form" id="itemForm" style="display: none;">
            <div class="form-row">
                <div class="form-group">
                    <label>اسم المادة</label>
                    <input type="text" id="itemName" placeholder="مثال: ورق A4" required>
                </div>
                <div class="form-group">
                    <label>الكمية</label>
                    <input type="number" id="itemQuantity" placeholder="1" min="1" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>السعر</label>
                    <input type="number" id="itemPrice" placeholder="0" min="0" step="1" required>
                </div>
                <div class="form-group">
                    <label>العملة</label>
                    <select id="itemCurrency" required>
                        <option value="syp">ليرة سورية</option>
                        <option value="usd">دولار أمريكي</option>
                    </select>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" onclick="cancelFormItem()" class="btn-secondary">
                    <i data-lucide="x"></i>
                    إلغاء
                </button>
                <button type="button" onclick="saveFormItem()" class="btn-primary">
                    <i data-lucide="save"></i>
                    <span id="itemSubmitText">إضافة</span>
                </button>
            </div>
        </div>

        <button type="button" onclick="showAddFormItem()" class="btn-primary" id="addItemBtn">
            <i data-lucide="plus"></i>
            إضافة مادة
        </button>
    @endif
</div>

@if($editable)
<script>
let formItems = @json($items->toArray() ?? []);
let editingIndex = -1;

function showAddFormItem() {
    document.getElementById('itemForm').style.display = 'block';
    document.getElementById('addItemBtn').style.display = 'none';
    document.getElementById('itemSubmitText').textContent = 'إضافة';
    clearFormItem();
    editingIndex = -1;
}

function editFormItem(index) {
    const item = formItems[index];
    document.getElementById('itemForm').style.display = 'block';
    document.getElementById('addItemBtn').style.display = 'none';
    document.getElementById('itemSubmitText').textContent = 'تحديث';
    
    document.getElementById('itemName').value = item.item_name;
    document.getElementById('itemQuantity').value = item.quantity;
    document.getElementById('itemPrice').value = item.price;
    document.getElementById('itemCurrency').value = item.currency;
    
    editingIndex = index;
}

function saveFormItem() {
    const name = document.getElementById('itemName').value;
    const quantity = document.getElementById('itemQuantity').value;
    const price = document.getElementById('itemPrice').value;
    const currency = document.getElementById('itemCurrency').value;
    
    if (!name || !quantity || !price) return;
    
    const item = {
        item_name: name,
        quantity: parseInt(quantity),
        price: parseFloat(price),
        currency: currency
    };
    
    if (editingIndex >= 0) {
        formItems[editingIndex] = item;
    } else {
        formItems.push(item);
    }
    
    updateItemsDisplay();
    updateHiddenInputs();
    cancelFormItem();
}

function removeFormItem(index) {
    formItems.splice(index, 1);
    updateItemsDisplay();
    updateHiddenInputs();
}

function cancelFormItem() {
    document.getElementById('itemForm').style.display = 'none';
    document.getElementById('addItemBtn').style.display = 'block';
    clearFormItem();
    editingIndex = -1;
}

function clearFormItem() {
    document.getElementById('itemName').value = '';
    document.getElementById('itemQuantity').value = '';
    document.getElementById('itemPrice').value = '';
    document.getElementById('itemCurrency').value = 'syp';
}

function updateItemsDisplay() {
    const container = document.getElementById('itemsList');
    if (formItems.length === 0) {
        container.innerHTML = '<div class="empty-items"><div class="empty-icon"><i data-lucide="package-x"></i></div><h4>لا توجد مواد مضافة</h4><p>قم بإضافة مواد لهذه الطلبية</p></div>';
    } else {
        container.innerHTML = formItems.map((item, index) => `
            <div class="item-card" data-index="${index}">
                <div class="item-info">
                    <div class="item-name">${item.item_name}</div>
                    <div class="item-details">
                        <span>الكمية: ${item.quantity}</span>
                        <span>السعر: ${item.price.toLocaleString()} ${item.currency == 'usd' ? 'دولار' : 'ليرة'}</span>
                    </div>
                </div>
                <div class="item-actions">
                    <button type="button" class="action-btn edit" onclick="editFormItem(${index})" title="تعديل">
                        <i data-lucide="edit-2"></i>
                    </button>
                    <button type="button" class="action-btn delete" onclick="confirmDeleteItem(${index})" title="حذف">
                        <i data-lucide="trash-2"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function confirmDeleteItem(index) {
    if (confirm('هل أنت متأكد من حذف هذه المادة؟')) {
        removeFormItem(index);
    }
}

function updateHiddenInputs() {
    const existingInputs = document.querySelectorAll('input[name^="items["]');
    existingInputs.forEach(input => input.remove());
    
    const form = document.querySelector('form');
    formItems.forEach((item, index) => {
        ['item_name', 'quantity', 'price', 'currency'].forEach(field => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `items[${index}][${field}]`;
            input.value = item[field];
            form.appendChild(input);
        });
    });
}
</script>
@endif