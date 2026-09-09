<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->boolean('is_admin')->default(false));
        Schema::create('festival_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('demo')->default(true);
            $table->boolean('sales_open')->default(true);
            $table->string('bank_name')->default('Demo Bank — do not transfer');
            $table->string('account_name')->default('IFAF DEMO ACCOUNT');
            $table->string('account_number')->default('0000000000');
            $table->unsignedInteger('capacity')->default(500);
            $table->unsignedInteger('hold_hours')->default(24);
            $table->json('ticket_options');
            $table->timestamps();
        });
        DB::table('festival_settings')->insert(['id' => 1, 'ticket_options' => json_encode(config('ifaf.tickets')), 'created_at' => now(), 'updated_at' => now()]);
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->string('reference')->unique();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 40);
            $table->string('ticket_name');
            $table->unsignedInteger('guests');
            $table->unsignedInteger('amount');
            $table->json('bank_details');
            $table->boolean('demo');
            $table->string('status')->default('awaiting_payment')->index();
            $table->timestamp('expires_at')->index();
            $table->string('sender_name')->nullable();
            $table->string('receipt_path')->nullable();
            $table->text('customer_note')->nullable();
            $table->text('review_note')->nullable();
            $table->string('bank_transaction')->nullable()->unique();
            $table->unsignedInteger('revision')->default(0);
            $table->timestamps();
        });
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('code', 64)->unique();
            $table->unsignedInteger('guest_number');
            $table->timestamp('checked_in_at')->nullable();
            $table->foreignId('checked_in_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->unique(['booking_id', 'guest_number']);
        });
        Schema::create('booking_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string('action');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_events');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('festival_settings');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_admin'));
    }
};
