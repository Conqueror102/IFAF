# IFAF manual ticket bookings

Customer flow: `/reserve` → private booking link → bank transfer receipt → admin verification → printable tickets with one QR per attendee.

Admin: `/admin/login`, with bookings, search, receipt review, activity history, settings and camera/code check-in. The bank transaction reference is required for approval and cannot be used twice. Refresh stale review pages before approving. Receipt files are private and only available to authenticated admins.

## Local demo

The initial settings use dummy bank details, sample prices, 500 demo places and a 24-hour payment window. Demo screens prohibit real transfers. Demo emails are not sent. Demo tickets cannot be checked in when the site is in live mode.

Run `php artisan migrate` and `php artisan ifaf:demo-admin`. The latter is local-only and saves a random password to `storage/app/private/demo-admin-access.txt`. It never overwrites an existing account. Use `php artisan ifaf:admin email@example.com` to create an administrator or reset its password interactively.

Create a demo reservation, upload a sample PNG/JPG/PDF (5 MB maximum), then sign in as admin to approve it with a unique dummy bank transaction reference. Open the private booking page to print/save the tickets as PDF. Use `/admin/scan` or the printed QR link to check in each attendee once. Camera scanning requires HTTPS or localhost. Manual code entry is always available.

## Before live sales

- Replace dummy bank details and confirm ticket names, prices, guest counts, capacity and reservation window in Admin → Settings. Prices are in whole NGN. Existing bookings retain their original settings.
- Configure a real mail transport and sender in `.env`, then verify delivery. Laravel's `log` mailer does not deliver email. Demo bookings intentionally never send email.
- Set the production `APP_URL`, `APP_ENV=production`, `APP_DEBUG=false`, HTTPS and secure session cookies. Create named administrator accounts and remove the local demo user.
- Run `php artisan migrate --force` during deployment. Run the Laravel scheduler (`php artisan schedule:run` every minute) to expire unpaid reservations. Expiry is also enforced when reservations are created, viewed or reviewed; payment submissions under review do not expire automatically.
- Protect and back up the database and `storage/app/private` together. Receipt uploads and booking links contain personal information. Define organiser contact details and a payment/refund policy before launch.

Approval sends a synchronous booking update for live bookings. Mail failure does not undo approval; it is recorded in the booking activity and the admin can resend. The private booking page always has the current status and tickets. QR generation and camera scanning run locally in the browser and do not send ticket data to an external QR service.

Checks: `php artisan test`, `vendor/bin/pint --test`, `vendor/bin/phpstan analyse`, `npm run build`.

## Returning customers

`/my-tickets` reads private booking shortcuts saved in localStorage on the same browser. It does not expose a public booking search or use email recovery. Shortcuts can be removed on shared devices; removing one does not cancel the booking. Clearing browser data or using a different device requires the original private link. Admin browsing does not add customer links to the admin's saved list.

Payment review checks the private status endpoint every 15 seconds while visible, and immediately on returning to the tab or reconnecting. A changed status refreshes the review page automatically. Sender name and notes are saved locally per booking while typing; file selections cannot survive a browser reload. Submitted receipts are stored on the server. The transfer reference has a clipboard button with success and failure feedback.
