<?php

namespace App\Mail;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationRequestReceived extends Mailable
{
  use Queueable, SerializesModels;

  protected $reserver, $reservation;
  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($reserver, Reservation $reservation)
  {
    $this->reserver = $reserver;
    $this->reservation = $reservation;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->markdown('emails.reservations.new_reservation', [
      'user' => $this->reserver,
      'reservation' => $this->reservation,
    ]);
  }
}
