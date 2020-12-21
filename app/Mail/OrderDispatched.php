<?php

namespace App\Mail;

use App\Models\Dispatcher;
use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderDispatched extends Mailable
{
  use Queueable, SerializesModels;
  protected $user, $order, $dispatcher;
  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct(User $user, Order $order, Dispatcher $dispatcher)
  {
    $this->user = $user;
    $this->order = $order;
    $this->dispatcher = $dispatcher;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->markdown('emails.orders.dispatched', [
      'user' => $this->user,
      'order' => $this->order,
      'dispatcher'=>$this->dispatcher,
    ]);
  }
}
