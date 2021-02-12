<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Wakafood extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'wakafood:install';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Automate Initial setup of the project';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    $this->line('Initialization Process Started!');
    $this->line('Step 1: Starting...');
    $this->comment('Database Migration');
    $now = now();
    $this->callSilently('migrate:fresh');
    $after = now();
    $this->info(sprintf('Step 1: Completed (%sms)', number_format($now->diffInMilliseconds($after))));

    $this->line('Step 2: Starting...');
    $this->comment('Passport Key Generation & Installation');
    $now = now();
    $this->callSilently('passport:keys', [
      '--length' => 512,
      '--force' => true,
    ]);
    $this->callSilently('passport:install');
    $after = now();
    $this->info(sprintf('Step 2: Completed (%sms)', number_format($now->diffInMilliseconds($after))));

    $this->line('Step 3: Starting...');
    $this->comment('Database Seeding');
    $now = now();
    $this->callSilently('db:seed');
    $after = now();
    $this->info(sprintf('Step 3: Completed (%sms)', number_format($now->diffInMilliseconds($after))));
    return 0;
  }
}
