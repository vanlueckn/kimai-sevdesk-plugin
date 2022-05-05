<?php

namespace KimaiPlugin\KimaiSevdeskBundle\Sevdesk;

use App\Export\ExportItemInterface;
use App\Export\RendererInterface;
use App\Repository\Query\TimesheetQuery;
use KimaiPlugin\KimaiSevdeskBundle\Configuration\SevdeskConfiguration;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SevdeskRenderer implements RendererInterface
{

    private HttpClientInterface $client;
    private SevdeskConfiguration $sevdeskConfiguration;

    public function __construct(HttpClientInterface $client, SevdeskConfiguration $sevdeskConfiguration)
    {
        $this->client = $client;
        $this->sevdeskConfiguration = $sevdeskConfiguration;
    }

    /**
     * @param ExportItemInterface[] $timesheets
     * @param TimesheetQuery $query
     * @return Response
     */
    public function render(array $timesheets, TimesheetQuery $query): Response
    {
        $sevdeskApiClient = new ApiClient($this->client, $this->sevdeskConfiguration);
        try {
            $sevdeskApiClient->createInvoice($timesheets);
        } catch (UnexpectedReturnValueException|ClientExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface|TransportExceptionInterface $e) {
            return new Response('Error creating sevdesk invoice' . "\n" . $e->getMessage() . "\n" . $e->getTraceAsString(), 400);
        }

        return new Response('Sevdesk Rechnung wurde erfolgreich erstellt.');
    }

    public function getId(): string
    {
        return 'ext_sevdesk_export';
    }

    public function getIcon(): string
    {
        return 'fas fa-file-code';
    }

    public function getTitle(): string
    {
        return 'sevdesk';
    }
}