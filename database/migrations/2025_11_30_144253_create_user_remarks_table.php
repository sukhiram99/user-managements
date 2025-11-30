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
        Schema::create('user_remarks', function (Blueprint $table) {
         $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('created_user_id')->constrained('users')->comment('Manager who added the remark');
            
            $table->text('old_remark')->nullable();
            $table->text('new_remark')->nullable();
            
            $table->boolean('is_seen')->default(false); // Has the user seen this remark?
            $table->foreignId('seen_user_id')->nullable()->constrained('users')->comment('User who marked as seen');

            $table->timestamp('seen_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Optional: Index for faster queries
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_remarks');
    }
};
