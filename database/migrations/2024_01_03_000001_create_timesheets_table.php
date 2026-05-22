<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('timesheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('project_task')->nullable();
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->integer('break_minutes')->default(0);
            $table->decimal('total_hours', 5, 2)->default(0);
            $table->string('status')->default('pending'); // pending, submitted, approved, rejected
            $table->date('week_start')->nullable();
            $table->date('week_end')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('timesheets'); }
};
