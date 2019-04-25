<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class VerificaEmail extends Notification
{
  use Queueable;
  public static $toMailCallback;

  /**
  * Create a new notification instance.
  *
  * @return void
  */
  public function __construct()
  {
    //
  }

  /**
  * Get the notification's delivery channels.
  *
  * @param  mixed  $notifiable
  * @return array
  */
  public function via($notifiable)
  {
    return ['mail'];
  }

  /**
  * Get the mail representation of the notification.
  *
  * @param  mixed  $notifiable
  * @return \Illuminate\Notifications\Messages\MailMessage
  */
  public function toMail($notifiable)
  {
    if (static::$toMailCallback) {
      return call_user_func(static::$toMailCallback, $notifiable);
    }

    return (new MailMessage)
    ->subject('Verifica indirizzo email')
    ->line('Clicca sul pulsante qui sotto per verificare il tuo indirizzo email')
    ->action(
      'Verifica indirizzo email',
      $this->verificationUrl($notifiable)
      )
      ->line('Se non hai creato nessun account, ignora questa email');
    }

    protected function verificationUrl($notifiable)
    {
      return URL::temporarySignedRoute(
        'verification.verify', Carbon::now()->addMinutes(60), ['id' => $notifiable->getKey()]
      );
    }

    public static function toMailUsing($callback)
    {
      static::$toMailCallback = $callback;
    }
  }
