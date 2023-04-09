<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OverdueBooksIndividual extends Mailable
{
use Queueable, SerializesModels;

protected $book_name;
protected $due_date;

/**
* Create a new message instance.
*
* @return void
*/
public function __construct($book_name, $due_date)
{
$this->book_name = $book_name;
$this->due_date = $due_date;
}

/**
* Get the message envelope.
*
* @return \Illuminate\Mail\Mailables\Envelope
*/
public function envelope()
{
return new Envelope(
subject: 'Overdue Books',
);
}

/**
* Get the message content definition.
*
* @return \Illuminate\Mail\Mailables\Content
*/
public function content()
{

    return new Content(
    view: 'mail.overdue-books-individual',
    with: [
    'book_name' => $this->book_name,
    'due_date' => $this->due_date
    ]
);
}

/**
* Get the attachments for the message.
*
* @return array
*/
public function attachments()
{
return [];
}
}