<?php declare(strict_types=1);

namespace OpenTraining\Statie\Posts\Year2017\NetteConfigObjects\Forms;

interface InvoiceFormNewFactoryInterface
{
    public function create(): InvoiceFormNew;
}
