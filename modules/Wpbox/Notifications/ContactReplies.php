<?php

namespace Modules\Wpbox\Notifications;

use App\NotificationChannels\Expo\ExpoChannel;
use App\NotificationChannels\Expo\ExpoMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ContactReplies extends Notification
{
    use Queueable;
    public $user;
    public $message;
    public $contact;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user,$message,$contact)
    {
        $this->user = $user;
        $this->message = $message;
        $this->contact = $contact;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $notificationClasses=[];
        if ($this->user != null && strlen($this->user->expotoken) > 3) {
            array_push($notificationClasses, ExpoChannel::class);
        }
        return $notificationClasses;
    }

    public function toExpo($notifiable)
    {

        try {
            return ExpoMessage::create()
                ->title($this->contact->name)
                ->body($this->message->value)
                ->badge(1);
        } catch (\Throwable $th) {
            //throw $th;
        }

    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', 'https://laravel.com')
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
