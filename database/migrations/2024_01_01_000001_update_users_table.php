<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('employee')->after('email'); // admin, employee
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete()->after('role');
            $table->string('designation')->nullable()->after('department_id');
            $table->string('phone')->nullable()->after('designation');
            $table->string('avatar')->nullable()->after('phone');
            $table->string('status')->default('active')->after('avatar'); // active, inactive, on_leave
            $table->date('date_of_birth')->nullable()->after('status');
            $table->date('date_joined')->nullable()->after('date_of_birth');
            $table->string('invitation_token')->nullable()->after('date_joined');
            $table->timestamp('account_setup_at')->nullable()->after('invitation_token');
            $table->foreignId('invited_by')->nullable()->constrained('users')->nullOnDelete()->after('account_setup_at');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role','department_id','designation','phone','avatar','status','date_of_birth','date_joined','invitation_token','account_setup_at','invited_by']);
        });
    }
};
