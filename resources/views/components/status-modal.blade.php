<div id="statusModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>تغيير حالة الطلبية</h3>
            <button type="button" class="close-btn" onclick="closeStatusModal()">
                <i data-lucide="x"></i>
            </button>
        </div>
        <form id="statusForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="modal-body">
                <div class="form-group">
                    <label>الحالة الجديدة</label>
                    <select id="newStatus" name="status" required>
                        <option value="new">جديدة</option>
                        <option value="in-progress">قيد التنفيذ</option>
                        <option value="ready">جاهزة</option>
                        <option value="delivered">تم التسليم</option>
                        <option value="archived">مؤرشفة</option>
                        <option value="cancelled">ملغاة</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeStatusModal()">إلغاء</button>
                <button type="submit" class="btn-primary">حفظ</button>
            </div>
        </form>
    </div>
</div>

<style>
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .modal-content {
        background: white;
        border-radius: 8px;
        width: 90%;
        max-width: 400px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-body {
        padding: 20px;
    }

    .modal-footer {
        padding: 20px;
        border-top: 1px solid #eee;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        align-items: end;
    }

    .close-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px;
    }

    .btn-secondary,
    .btn-primary {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 40px;
        box-sizing: border-box;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #545b62;
    }

    .btn-primary {
        background: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background: #0056b3;
    }
</style>

<script>
    function showStatusModal(orderId, currentStatus) {
        const modal = document.getElementById('statusModal');
        const form = document.getElementById('statusForm');
        const select = document.getElementById('newStatus');

        form.action = '{{ route('orders.update-status', ':id') }}'.replace(':id', orderId);
        select.value = currentStatus;
        modal.style.display = 'flex';
    }

    function closeStatusModal() {
        document.getElementById('statusModal').style.display = 'none';
    }
</script>
