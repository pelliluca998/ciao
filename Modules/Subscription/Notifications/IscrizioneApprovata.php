<?php

namespace Modules\Subscription\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class IscrizioneApprovata extends Notification
{
  use Queueable;
  private $id_subscription;
  private $nome_evento;

  /**
  * Create a new notification instance.
  *
  * @return void
  */
  public function __construct($id_subscription, $nome_evento)
  {
    $this->id_subscription = $id_subscription;
    $this->nome_evento = $nome_evento;
  }

  /**
  * Get the notification's delivery channels.
  *
  * @param mixed $notifiable
  * @return array
  */
  public function via($notifiable)
  {
    return ['mail'];
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
    ->subject('[Segresta] Iscrizione all\'evento approvata')
    ->greeting('Ciao!')
    ->line("Ciao ".$notifiable->full_name.", l'iscrizione numero #".$this->id_subscription." all'evento '".$this->nome_evento."' è stata approvata")
    ->line('Accedi alla tua area riservata per maggiori informazioni.')
    ->action('Accedi a Segresta', config('app.url'));
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
