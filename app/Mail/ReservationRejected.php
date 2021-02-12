<?php

namespace App\Mail;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationRejected extends Mailable
{
  use Queueable, SerializesModels;

  protected $user, $reservation;
  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct(User $user, Reservation $reservation)
  {
    $this->user = $user;
    $this->reservation = $reservation;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->markdown('emails.reservations.rejected', [
      'user' => $this->user,
      'reservation' => $this->reservation,
    ]);
  }
}
