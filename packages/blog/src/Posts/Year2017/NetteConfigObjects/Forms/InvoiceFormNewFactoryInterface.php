<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Posts\Year2017\NetteConfigObjects\Forms;

interface InvoiceFormNewFactoryInterface
{
    public function create(): InvoiceFormNew;
}
