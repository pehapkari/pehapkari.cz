<?php declare(strict_types=1);

namespace Pehapkari\Registration\Api\Fakturoid;

final class FakturoidEndpoint
{
    /**
     * @var string
     * @see https://fakturoid.docs.apiary.io/#reference/invoices/invoices-collection/nova-faktura
     */
    public const POST_NEW_INVOICE = self::BASE_API_URL . '/invoices.json';

    /**
     * @var string
     * @see https://fakturoid.docs.apiary.io/#reference/invoices/invoice/detail-faktury
     */
    public const GET_INVOICE_DETAIL = self::BASE_API_URL . '/invoices/%s.json';

    /**
     * @var string
     */
    public const GET_SEARCH_CONTACT = self::BASE_API_URL . '/subjects/search.json?query=%s';

    /**
     * @var string
     * @see https://fakturoid.docs.apiary.io/#reference/subjects/subjects-collection/novy-kontakt
     */
    public const POST_NEW_CONTACT = self::BASE_API_URL . '/subjects.json';

    /**
     * @var string
     */
    private const BASE_API_URL = 'https://app.fakturoid.cz/api/v2/accounts/%s';
}
