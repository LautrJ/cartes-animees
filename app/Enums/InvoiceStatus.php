<?php

namespace App\Enums;

enum InvoiceStatus: string implements \Filament\Support\Contracts\HasLabel
{
    case Draft = 'draft';
    case Open = 'open';
    case Paid = 'paid';
    case Uncollectible = 'uncollectible';
    case Void = 'void';

    public function getLabel(): ?string
    {
        return __('enums.invoice_status.' . $this->value);
    }
}
