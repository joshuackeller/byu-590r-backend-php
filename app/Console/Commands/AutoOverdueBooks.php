<?php

namespace App\Console\Commands;

use App\Mail\OverdueBooksMasterList;
use App\Mail\OverdueBooksIndividual;
use App\Models\Checkout;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class AutoOverdueBooks extends Command
{
/**
* The name and signature of the console command.
*
* @var string
*/
protected $signature = 'auto:overdue-books {--email=}';

/**
* The console command description.
*
* @var string
*/
protected $description = 'Returns a list of all overdue books to the admin user AND emails all overdue people';

/**
* Execute the console command.
*
* @return int
*/
public function handle()
{
    $sendToEmail = $this->option('email');
    if(!$sendToEmail) {
    return Command::FAILURE;
    }

     $overDueCheckouts = DB::select(
        'SELECT users.name as user_name, users.email as user_email, books.name as book_name, checkouts.due_date as due_date 
        FROM user_book_checkouts 
        JOIN users ON user_book_checkouts.user_id = users.id 
        JOIN books ON user_book_checkouts.book_id = books.id 
        JOIN checkouts ON user_book_checkouts.checkout_id = checkouts.id 
        WHERE checkouts.checkin_date IS NULL
        AND checkouts.due_date <= CURDATE()
        ');

    if (count($overDueCheckouts) > 0) {
        // Send one main list of all overdue books email to management
        Mail::to($sendToEmail)->send(new OverdueBooksMasterList($overDueCheckouts));

        foreach($overDueCheckouts as $overDueCheckout) {
            // Send individual emails to each user with their overdue books
            Mail::to($overDueCheckout->user_email)->send(new OverdueBooksIndividual($overDueCheckout->book_name, $overDueCheckout->due_date));
        }
    }

    return Command::SUCCESS;
}
}