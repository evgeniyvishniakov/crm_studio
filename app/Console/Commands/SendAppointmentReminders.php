<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Clients\Appointment;
use App\Services\EmailNotificationService;
use Carbon\Carbon;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders for tomorrow appointments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting appointment reminders...');
        
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        
        // Получаем записи на завтра
        $appointments = Appointment::with(['client', 'service', 'user', 'project'])
            ->where('date', $tomorrow)
            ->where('status', 'pending')
            ->whereHas('client', function($query) {
                $query->whereNotNull('email')->where('email', '!=', '');
            })
            ->whereHas('project', function($query) {
                $query->where('email_notifications_enabled', true);
            })
            ->get();
        
        $this->info("Found {$appointments->count()} appointments for tomorrow");
        
        $emailService = new EmailNotificationService();
        $sentCount = 0;
        $errorCount = 0;
        
        foreach ($appointments as $appointment) {
            try {
                $sent = $emailService->sendReminder($appointment);
                
                if ($sent) {
                    $sentCount++;
                    $this->info("✓ Reminder sent to {$appointment->client->email} for {$appointment->service->name}");
                } else {
                    $errorCount++;
                    $this->error("✗ Failed to send reminder to {$appointment->client->email}");
                }
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("✗ Error sending reminder: " . $e->getMessage());
            }
        }
        
        $this->info("Reminders completed: {$sentCount} sent, {$errorCount} errors");
        
        return 0;
    }
}