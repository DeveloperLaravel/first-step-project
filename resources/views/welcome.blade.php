<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>نظام إدارة المستخدمين</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <div class="min-h-screen flex items-center justify-center">

        <div class="max-w-4xl w-full bg-white rounded-2xl shadow-lg p-8">

            {{-- 🔥 Header --}}
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-800">
                    👋 مرحبًا بك في النظام
                </h1>

                <p class="text-gray-500 mt-3">
                    نظام احترافي لإدارة المستخدمين والصلاحيات
                </p>
            </div>

            {{-- 📊 Features --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="p-6 bg-blue-50 rounded-xl text-center">
                    <h2 class="text-xl font-bold text-blue-600">👤 المستخدمين</h2>
                    <p class="text-gray-500 mt-2">
                        إدارة كاملة للمستخدمين
                    </p>
                </div>

                <div class="p-6 bg-green-50 rounded-xl text-center">
                    <h2 class="text-xl font-bold text-green-600">🛡️ الأدوار</h2>
                    <p class="text-gray-500 mt-2">
                        التحكم في الأدوار بسهولة
                    </p>
                </div>

                <div class="p-6 bg-purple-50 rounded-xl text-center">
                    <h2 class="text-xl font-bold text-purple-600">🔐 الصلاحيات</h2>
                    <p class="text-gray-500 mt-2">
                        نظام صلاحيات قوي
                    </p>
                </div>

            </div>

            {{-- 🚀 Button --}}
            <div class="text-center mt-10">
                <a href="/admin/login"
                   class="px-8 py-3 bg-indigo-600 text-white rounded-xl text-lg font-semibold hover:bg-indigo-700 transition">
                    🚀 الدخول إلى لوحة التحكم
                </a>
            </div>

        </div>

    </div>

</body>
</html>
