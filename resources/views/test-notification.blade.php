@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-bell"></i> اختبار إرسال الإشعارات - OneSignal</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('test-notification.send') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="player_id" class="form-label fw-bold">Player ID</label>
                                <input type="text" class="form-control" id="player_id" name="player_id"
                                    value="79ede21e-269c-4bea-997a-0c4ebf8db49d" required>
                                <small class="form-text text-muted">معرف المستخدم في OneSignal</small>
                            </div>

                            <div class="mb-3">
                                <label for="title" class="form-label fw-bold">عنوان الإشعار</label>
                                <input type="text" class="form-control" id="title" name="title"
                                    value="إشعار تجريبي" required>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label fw-bold">نص الإشعار</label>
                                <textarea class="form-control" id="message" name="message" rows="3" required>هذا إشعار تجريبي من النظام</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-paper-plane"></i> إرسال الإشعار
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                @if (session('success'))
                    <div class="card shadow-sm border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-check-circle"></i> نجح الإرسال</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success mb-3">
                                {{ session('success') }}
                            </div>

                            @if (session('response'))
                                <h6 class="fw-bold">استجابة OneSignal:</h6>
                                <div class="bg-light p-3 rounded">
                                    <pre class="mb-0">{{ json_encode(session('response'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="card shadow-sm border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> خطأ في الإرسال</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-danger mb-3">
                                {{ session('error') }}
                            </div>
                            
                            @if (session('response'))
                                <h6 class="fw-bold">تفاصيل الخطأ:</h6>
                                <div class="bg-light p-3 rounded">
                                    <pre class="mb-0">{{ is_string(session('response')) ? session('response') : json_encode(session('response'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if (!session('success') && !session('error'))
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-info-circle"></i> معلومات</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>Player ID المحدد:</strong> 72bb4d6b-1f28-43bf-ac2a-fd0dafc697bc</p>
                            <p class="mb-2"><strong>الخدمة:</strong> OneSignal Push Notifications</p>
                            <p class="mb-0"><strong>الحالة:</strong> جاهز للإرسال</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        /* تحسين التصميم العام */
        .container-fluid {
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .card {
            margin-bottom: 20px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            border-radius: 15px 15px 0 0 !important;
            padding: 15px 20px;
            font-weight: bold;
        }

        .card-body {
            padding: 25px;
        }

        /* تحسين النماذج */
        .form-label {
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            transform: translateY(-2px);
        }

        /* تحسين الأزرار */
        .btn {
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* تحسين الرسائل */
        .alert {
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
            font-weight: 500;
        }

        /* تحسين عرض JSON */
        pre {
            font-size: 12px;
            max-height: 300px;
            overflow-y: auto;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            color: #495057;
            font-family: 'Courier New', monospace;
        }

        /* تحسين الألوان */
        .bg-primary {
            background: linear-gradient(45deg, #007bff, #0056b3) !important;
        }

        .bg-success {
            background: linear-gradient(45deg, #28a745, #1e7e34) !important;
        }

        .bg-danger {
            background: linear-gradient(45deg, #dc3545, #bd2130) !important;
        }

        .bg-info {
            background: linear-gradient(45deg, #17a2b8, #117a8b) !important;
        }

        /* تحسين الأيقونات */
        i.fas {
            margin-left: 8px;
        }

        /* تحسين النصوص */
        .text-muted {
            font-size: 13px;
            margin-top: 5px;
        }

        /* تأثيرات إضافية */
        .shadow-sm {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
        }

        /* تحسين الاستجابة للشاشات الصغيرة */
        @media (max-width: 768px) {
            .container-fluid {
                padding: 10px;
            }

            .card-body {
                padding: 20px;
            }

            .btn-lg {
                padding: 15px 20px;
                font-size: 16px;
            }
        }
    </style>
@endsection
