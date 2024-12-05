<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransactionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $transaction;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param $transaction
     */
    public function __construct($user,$transaction)
    {
        $this->user = $user;
        $this->transaction=$transaction;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'وضعیت تراکنش', // موضوع ایمیل
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            text: 'emails.transaction-text', // آدرس فایل متن ساده
            with: [
                'name' => $this->user->name,
                'order_id' =>$this->transaction->order_id,
                'amount' => $this->transaction->amount,
                'status' => $this->transaction->status,
                'token' => $this->transaction->token,
                ''
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments(): array
    {
        return [];
    }
}
