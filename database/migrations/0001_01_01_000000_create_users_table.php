<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            // ايقاف الخدمه
              //تسجيل تاريخ التعطيل
            $table->boolean('is_active')->default(true);
            $table->timestamp('deactivated_at')->nullable();

                  // تتبع آخر تسجيل دخول
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
// آخر مرة كان نشط داخل النظام
$table->timestamp('last_seen_at')->nullable();
$table->string('user_agent')->nullable();
            $table->rememberToken();

              // Soft delete (مهم جدًا في الأنظمة)
            $table->softDeletes();
            $table->timestamps();

        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
