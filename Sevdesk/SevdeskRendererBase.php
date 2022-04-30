<?php

namespace KimaiPlugin\KimaiSevdesk\Sevdesk;

use App\Export\Base\RendererTrait;
use App\Export\ExportItemInterface;
use App\Repository\Query\TimesheetQuery;
use KimaiPlugin\KimaiSevdesk\Configuration\SevdeskConfiguration;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SevdeskRendererBase
{
    use RendererTrait;

    private HttpClientInterface $client;
    private SevdeskConfiguration $sevdeskConfiguration;
    private string $id = 'sevdesk';

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
            return new Response('Error creating sevdesk invoice', 400);
        }

        return new Response('Sevdesk Rechnung wurde erfolgreich erstellt.');
    }

    public function setId(string $id): SevdeskRendererBase
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }
}