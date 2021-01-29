<?php

namespace App\Mail;

use App\Models\TableReservation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationRequestReceived extends Mailable
{
  use Queueable, SerializesModels;

  protected $user, $reservation;
  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct(User $user, TableReservation $reservation)
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
    return $this->markdown('emails.table_reservations.new_reservation', [
      'user' => $this->user,
      'reservation' => $this->reservation,
    ]);
  }
}
