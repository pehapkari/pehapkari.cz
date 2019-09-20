<?php declare(strict_types=1);

namespace Pehapkari\Registration\Api\Fakturoid;

final class FakturoidEndpoints
{
    /**
     * POST
     * @var string
     * @see https://fakturoid.docs.apiary.io/#reference/invoices/invoices-collection/nova-faktura
     */
    public const NEW_INVOICE = self::BASE_API_URL . '/invoices.json';

    /**
     * POST
     * @var string
     * @see https://fakturoid.docs.apiary.io/#reference/invoices/invoice-actions/akce-nad-fakturou
     */
    public const INVOICE_ACTION = self::BASE_API_URL . '/invoices/%s/fire.json';

    /**
     * GET
     * @var string
     * @see https://fakturoid.docs.apiary.io/#reference/invoices/invoice/detail-faktury
     */
    public const INVOICE_DETAIL = self::BASE_API_URL . '/invoices/%s.json';

    /**
     * GET
     * @var string
     */
    public const INVOICES = self::BASE_API_URL . '/invoices.json';

    /**
     * GET
     * @var string
     */
    public const SEARCH_CONTACT = self::BASE_API_URL . '/subjects/search.json?query=%s';

    /**
     * POST
     * @var string
     * @see https://fakturoid.docs.apiary.io/#reference/subjects/subjects-collection/novy-kontakt
     */
    public const NEW_CONTACT = self::BASE_API_URL . '/subjects.json';

    /**
     * @var string
     */
    private const BASE_API_URL = 'https://app.fakturoid.cz/api/v2/accounts/%s';
}
