<?php

use App\Models\Booking;
use App\Models\FestivalSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

function reserveDemo(array $overrides = []): Booking
{
    test()->post(route('booking.store'), array_merge([
        'name' => 'Test Guest', 'email' => 'guest@example.test', 'phone' => '08012345678', 'option' => 1, 'consent' => '1',
    ], $overrides))->assertRedirect();

    return Booking::latest('id')->firstOrFail();
}

function submitDemo(Booking $booking): void
{
    test()->post(route('booking.payment', $booking), [
        'sender_name' => 'Test Sender', 'receipt' => UploadedFile::fake()->create('receipt.pdf', 20, 'application/pdf'), 'acknowledgement' => '1',
    ])->assertRedirect()->assertSessionHasNoErrors();
    $booking->refresh();
}

function adminUser(): User
{
    $user = User::factory()->create();
    $user->is_admin = true;
    $user->save();

    return $user;
}

test('reservation prices and guest counts are set by server and private links are required', function () {
    $booking = reserveDemo(['amount' => 1, 'guests' => 100]);
    expect($booking->amount)->toBe(10000)->and($booking->guests)->toBe(2)->and($booking->demo)->toBeTrue();
    $this->get(route('booking.show', $booking))->assertOk()->assertSee('Do not send money')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    $this->get('/booking/'.$booking->id)->assertNotFound();
    $this->get(route('booking.tickets', $booking))->assertForbidden();
});

test('manual approval creates one ticket per guest and check-in can only happen once', function () {
    Storage::fake('local');
    $booking = reserveDemo();
    submitDemo($booking);
    Storage::disk('local')->assertExists($booking->receipt_path);
    $admin = adminUser();
    $review = ['decision' => 'confirmed', 'revision' => $booking->revision, 'bank_transaction' => 'DEMO-001', 'verified' => '1'];
    $this->actingAs($admin)->post(route('admin.review', $booking), $review)->assertSessionHasNoErrors();
    expect($booking->refresh()->status)->toBe('confirmed')->and($booking->tickets()->count())->toBe(2);
    $this->get(route('booking.tickets', $booking))->assertOk()
        ->assertSee('DEMO · NOT VALID FOR ENTRY')
        ->assertSee('Download PNG')
        ->assertSee('data-download-ticket', false)
        ->assertDontSee('Print / Save as PDF')
        ->assertDontSee('Bridging the divide.')
        ->assertDontSee('International Food<br>&amp; Arts Festival', false);
    $this->post(route('admin.review', $booking), $review)->assertSessionHasErrors('decision');
    expect($booking->tickets()->count())->toBe(2);
    $ticket = $booking->tickets()->first();
    $this->post(route('admin.checkin', $ticket))->assertSessionHasNoErrors();
    $this->post(route('admin.checkin', $ticket))->assertSessionHasErrors('ticket');
});

test('unapproved users cannot access reviews receipts settings or check-in', function () {
    $booking = reserveDemo();
    $this->get(route('admin.index'))->assertRedirect(route('admin.login'));
    $this->actingAs(User::factory()->create())->get(route('admin.index'))->assertForbidden();
    $this->get(route('admin.receipt', $booking))->assertForbidden();
    $this->post(route('admin.review', $booking), [])->assertForbidden();
    $this->get(route('admin.settings'))->assertForbidden();
    $this->get(route('admin.scan'))->assertForbidden();
});

test('capacity and expiry are enforced and submitted payments stay reserved', function () {
    Storage::fake('local');
    FestivalSetting::find(1)->update(['capacity' => 2]);
    $booking = reserveDemo();
    $this->post(route('booking.store'), ['name' => 'Other', 'email' => 'other@example.test', 'phone' => '08012345678', 'option' => 0, 'consent' => 1])->assertSessionHasErrors('option');
    $this->travel(25)->hours();
    $this->post(route('booking.payment', $booking), ['sender_name' => 'Late', 'receipt' => UploadedFile::fake()->create('r.pdf', 20, 'application/pdf'), 'acknowledgement' => 1])->assertSessionHasErrors('receipt');
    $this->get(route('booking.show', $booking))->assertSee('Reservation expired');
    expect(Storage::disk('local')->allFiles('receipts'))->toBe([]);
    $next = reserveDemo();
    submitDemo($next);
    $this->travel(48)->hours();
    $this->get(route('booking.show', $next))->assertSee('Payment under review');
});

test('duplicate transaction and stale review cannot approve another booking', function () {
    Storage::fake('local');
    $first = reserveDemo();
    submitDemo($first);
    $second = reserveDemo();
    submitDemo($second);
    $this->actingAs(adminUser());
    $this->post(route('admin.review', $first), ['decision' => 'confirmed', 'revision' => 1, 'bank_transaction' => 'CREDIT-123', 'verified' => 1])->assertSessionHasNoErrors();
    $this->post(route('admin.review', $second), ['decision' => 'confirmed', 'revision' => 1, 'bank_transaction' => 'credit-123', 'verified' => 1])->assertSessionHasErrors('bank_transaction');
    $this->post(route('admin.review', $second), ['decision' => 'confirmed', 'revision' => 0, 'bank_transaction' => 'CREDIT-456', 'verified' => 1])->assertSessionHasErrors('decision');
    expect($second->refresh()->status)->toBe('under_review')->and($second->tickets()->count())->toBe(0);
});

test('clarification permits a new receipt and invalid uploads are refused', function () {
    Storage::fake('local');
    $booking = reserveDemo();
    $this->post(route('booking.payment', $booking), ['sender_name' => 'Sender', 'receipt' => UploadedFile::fake()->create('bad.html', 5, 'text/html'), 'acknowledgement' => 1])->assertSessionHasErrors('receipt');
    submitDemo($booking);
    $this->actingAs(adminUser())->post(route('admin.review', $booking), ['decision' => 'needs_information', 'revision' => 1, 'review_note' => 'Please send a clearer receipt.'])->assertSessionHasNoErrors();
    $this->get(route('booking.show', $booking))->assertSee('Please send a clearer receipt.');
    submitDemo($booking);
    expect($booking->status)->toBe('under_review')->and($booking->revision)->toBe(3);
});

test('demo tickets are refused for live admission and QR lookup resolves safely', function () {
    $booking = reserveDemo();
    $booking->update(['status' => 'confirmed']);
    $ticket = $booking->tickets()->create(['guest_number' => 1, 'code' => Str::random(48)]);
    FestivalSetting::find(1)->update(['demo' => false]);
    $this->actingAs(adminUser())->post(route('admin.lookup'), ['code' => route('admin.ticket', $ticket)])->assertRedirect(route('admin.ticket', $ticket));
    $this->post(route('admin.checkin', $ticket))->assertSessionHasErrors('ticket');
});

test('all booking and admin screens render', function () {
    $this->get(route('booking.create'))->assertOk();
    $this->get(route('admin.login'))->assertOk();
    $booking = reserveDemo();
    $this->actingAs(adminUser());
    foreach (['admin.index', 'admin.settings', 'admin.scan'] as $route) {
        $this->get(route($route))->assertOk();
    }
    $this->get(route('admin.booking', $booking))->assertOk();
});

test('booking status updates are private and return the current state without personal details', function () {
    $booking = reserveDemo();
    $this->getJson(route('booking.status', $booking))
        ->assertOk()->assertExactJson(['status' => 'awaiting_payment', 'label' => 'Awaiting payment'])
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    $booking->update(['status' => 'confirmed']);
    $this->getJson(route('booking.status', $booking))->assertJsonPath('status', 'confirmed');
    $this->getJson('/booking/'.$booking->id.'/status')->assertNotFound();
});

test('my tickets page does not expose a server-side customer list', function () {
    $booking = reserveDemo();
    $this->get(route('booking.saved'))->assertOk()->assertSee('data-saved-bookings', false)
        ->assertDontSee($booking->token)->assertDontSee($booking->email);
    $this->get(route('booking.show', $booking))->assertSee('data-save-booking', false)->assertSee('Copy reference');
    $this->actingAs(adminUser())->get(route('booking.show', $booking))->assertDontSee('data-save-booking', false);
});
