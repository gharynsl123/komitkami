<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DocumentRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $docType = $this->data['doc_type'] ?? 'Dokumen';
        $productTitle = $this->data['product_title'] ?? 'Produk';

        return $this->subject("[Permintaan Dokumen] {$docType} - {$productTitle}")
                    ->view('emails.document-request')
                    ->with($this->data);
    }
}

