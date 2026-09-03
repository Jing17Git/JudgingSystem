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
        Schema::table('audit_records', function (Blueprint $table) {
            $table->decimal('old_score', 5, 2)->nullable()->after('candidate_number');
            $table->decimal('new_score', 5, 2)->nullable()->after('old_score');
            $table->string('user_agent')->nullable()->after('ip_address');
            $table->string('risk_level')->default('normal')->after('status'); // normal, warning, critical
            $table->boolean('is_suspicious')->default(false)->after('risk_level');
            $table->text('suspicious_reason')->nullable()->after('is_suspicious');
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete()->after('suspicious_reason');
            $table->string('reviewer_name')->nullable()->after('reviewer_id');
            $table->text('review_notes')->nullable()->after('reviewer_name');
            $table->timestamp('reviewed_at')->nullable()->after('review_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_records', function (Blueprint $table) {
            $table->dropForeign(['reviewer_id']);
            $table->dropColumn([
                'old_score',
                'new_score',
                'user_agent',
                'risk_level',
                'is_suspicious',
                'suspicious_reason',
                'reviewer_id',
                'reviewer_name',
                'review_notes',
                'reviewed_at',
            ]);
        });
    }
};
