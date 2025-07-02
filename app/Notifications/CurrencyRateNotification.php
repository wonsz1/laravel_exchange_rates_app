<?php

namespace App\Notifications;

use App\Models\Currency;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Message;
use Illuminate\Notifications\MailMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\Messages\MailMessage as NotificationMailMessage;
use Illuminate\Notifications\Notification;

class CurrencyRateNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private Currency $fromCurrency,
        private Currency $toCurrency,
        private float $currentRate,
        private float $threshold,
        private string $direction
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(mixed $notifiable): NotificationMailMessage
    {
        $directionText = match ($this->direction) {
            'above' => 'above',
            'below' => 'below',
            default => 'at'
        };

        return (new NotificationMailMessage)
            ->subject("Currency Rate Alert: {$this->fromCurrency->symbol}/{$this->toCurrency->symbol}")
            ->greeting('Currency Rate Alert')
            ->line("The current exchange rate between {$this->fromCurrency->symbol} and {$this->toCurrency->symbol} is {$this->currentRate}")
            ->line("This rate is {$directionText} your threshold of {$this->threshold}")
            ->action('View Currency Rates', url('/currency-rates'))
            ->line('Thank you for using our currency rate notification service!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
