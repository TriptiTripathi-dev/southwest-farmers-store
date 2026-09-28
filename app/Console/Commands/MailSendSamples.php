<?php

namespace App\Console\Commands;

use App\Mail\NewsletterMail;
use App\Mail\SupportTicketCreated;
use App\Mail\SupportTicketReplied;
use App\Mail\SupportTicketStatusChanged;
use App\Models\StoreUser;
use App\Models\SupportTicket;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * php artisan mail:send-samples someone@example.com
 *
 * Sends ONE of every email the store app sends, all to the given address,
 * using existing records read-only (nothing is created or changed). Prints
 * "sent" or the mail server's exact reason per email.
 */
class MailSendSamples extends Command
{
    protected $signature = 'mail:send-samples {to : Address every sample email goes to}';

    protected $description = 'Send one sample of every store email to one address (read-only)';

    public function handle(): int
    {
        $to = $this->argument('to');
        $ticket = SupportTicket::with('messages')->whereHas('messages')->latest('id')->first();

        $samples = [
            // An unsaved user just carries the address: nothing is written.
            'Password reset' => fn () => (new StoreUser(['name' => 'Sample', 'email' => $to]))->notify(new ResetPassword('sample-token-not-valid')),
            'Newsletter' => fn () => Mail::to($to)->send(new NewsletterMail('Sample newsletter - ' . config('app.name'), '<p>This is a sample newsletter to check email delivery.</p>')),
            'Support ticket created' => fn () => $ticket ? Mail::to($to)->send(new SupportTicketCreated($ticket)) : 'SKIPPED: no ticket with messages',
            'Support ticket replied' => fn () => $ticket ? Mail::to($to)->send(new SupportTicketReplied($ticket, $ticket->messages->last())) : 'SKIPPED: no ticket with messages',
            'Support ticket status changed' => fn () => $ticket ? Mail::to($to)->send(new SupportTicketStatusChanged($ticket)) : 'SKIPPED: no ticket with messages',
            'Plain test email' => fn () => Mail::raw('Plain test email from ' . config('app.name') . ' at ' . now()->toDateTimeString() . ' UTC.',
                fn ($m) => $m->to($to)->subject('Mail test - ' . config('app.name'))),
        ];

        $this->info('Mailer: ' . config('mail.default') . ' via ' . config('mail.mailers.' . config('mail.default') . '.host', '-') . '   From: ' . config('mail.from.address'));
        $rows = []; $failed = 0; $n = 0;
        foreach ($samples as $name => $send) {
            $n++;
            try {
                $result = $send();
                $status = is_string($result) && str_starts_with($result, 'SKIPPED') ? $result : 'sent';
            } catch (\Throwable $e) {
                $failed++;
                $message = trim(preg_replace('/\s+/', ' ', $e->getMessage()));
                if (preg_match('/with message "([^"]+)"/', $message, $m)) {
                    $message = $m[1];
                }
                $status = 'FAILED: ' . mb_strimwidth($message, 0, 220, '...');
            }
            $rows[] = [$n, $name, $status];
        }
        $this->table(['#', 'Email', 'Result'], $rows);
        $this->line("All sent to {$to}. Ticket used: #" . ($ticket->ticket_number ?? '-') . '.');

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
