<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\Order;
use App\Models\Purchase;
use Illuminate\Database\Seeder;

class AttachmentSeeder extends Seeder
{
    public function run()
    {
        $orders = Order::all();
        $purchases = Purchase::all();
        
        // Order attachments
        $orderAttachments = [
            [
                'attachable_type' => Order::class,
                'attachable_id' => $orders->first()->id,
                'file_name' => 'تصميم البطاقة.pdf',
                'file_path' => 'attachments/design1.pdf',
                'file_type' => 'application/pdf',
                'file_size' => 1024000,
            ],
            [
                'attachable_type' => Order::class,
                'attachable_id' => $orders->first()->id,
                'file_name' => 'لوجو الشركة.png',
                'file_path' => 'attachments/logo1.png',
                'file_type' => 'image/png',
                'file_size' => 512000,
            ],
            [
                'attachable_type' => Order::class,
                'attachable_id' => $orders->skip(1)->first()->id,
                'file_name' => 'محتوى الفلاير.docx',
                'file_path' => 'attachments/flyer_content.docx',
                'file_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'file_size' => 256000,
            ],
        ];

        // Purchase attachments
        $purchaseAttachments = [
            [
                'attachable_type' => Purchase::class,
                'attachable_id' => $purchases->first()->id,
                'file_name' => 'فاتورة الورق.pdf',
                'file_path' => 'purchase_attachments/paper_invoice.pdf',
                'file_type' => 'application/pdf',
                'file_size' => 768000,
            ],
            [
                'attachable_type' => Purchase::class,
                'attachable_id' => $purchases->skip(1)->first()->id,
                'file_name' => 'قائمة الأحبار.xlsx',
                'file_path' => 'purchase_attachments/ink_list.xlsx',
                'file_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'file_size' => 128000,
            ],
        ];

        $allAttachments = array_merge($orderAttachments, $purchaseAttachments);

        foreach ($allAttachments as $attachmentData) {
            Attachment::create($attachmentData);
        }
    }
}