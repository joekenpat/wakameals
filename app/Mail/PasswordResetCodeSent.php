<?php

namespace App\Notifications;

use App\Models\PasswordReset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Crypt;

class PasswordResetCodeSent extends Notification
{
  use Queueable;

  protected $account;
  protected $password_reset;

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct(Authenticatable $account, PasswordReset $password_reset)
  {
    $this->account = $account;
    $this->password_reset = $password_reset;
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
    return (new MailMessage)
      ->from('support@bellefu.com', 'Bellefu Support')
      ->subject('Confirm your password change!')
      ->greeting('Hello ' . $this->account->first_name ?? $this->account->name)
      ->line('Please, confirm the change of your password by clicking the button below:')
      ->action('RESET', sprintf(
        '%s/auth/confirm_password_reset?id=%s&token=%s',
        config('app.url'),
        Crypt::encryptString($this->account->email),
        Crypt::encryptString($this->password_reset->code)
      ))
      ->line('PS: We take your privacy very seriously. Never disclose your personal account password to anyone! Not even to our Representatives');
  }

  /**
   * Get the array representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function toArray($notifiable)
  {
    return [
      //
    ];
  }
}
