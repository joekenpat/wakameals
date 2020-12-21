<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderRecieved extends Mailable
{
  use Queueable, SerializesModels;

  protected $user, $order;
  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct(User $user, Order $order)
  {
    $this->user = $user;
    $this->order = $order;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->markdown('emails.orders.recieved', [
      'user' => $this->user,
      'order' => $this->order,
    ]);
  }
}
