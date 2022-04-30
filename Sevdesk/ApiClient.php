<?php

namespace KimaiPlugin\KimaiSevdesk\Sevdesk;

use App\Export\ExportItemInterface;
use DateTime;
use KimaiPlugin\KimaiSevdesk\Configuration\SevdeskConfiguration;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiClient
{
    private HttpClientInterface $client;
    private SevdeskConfiguration $sevdeskConfiguration;

    public function __construct(HttpClientInterface $client, SevdeskConfiguration $sevdeskConfiguration)
    {
        $this->client = $client;
        $this->sevdeskConfiguration = $sevdeskConfiguration;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws UnexpectedReturnValueException
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function getNextInvoiceNumber(): string
    {
        $response = $this->client->request(
            'GET',
            'https://my.sevdesk.de/api/v1/SevSequence/Factory/getByType?objectType=Invoice&type=RE&token=' . $this->sevdeskConfiguration->getSevdeskApiKey()
        );

        $responseObj = json_decode($response->getContent());

        if (!$responseObj || !$responseObj->objects || !$responseObj->objects->format || $responseObj->objects->nextSequence) {
            throw new UnexpectedReturnValueException('Unexpected return value');
        }

        $format = $responseObj->objects->format;
        $nextSequence = $responseObj->objects->nextSequence;

        return str_replace('%NUMBER', $nextSequence, $format);
    }

    /**
     * @param ExportItemInterface[] $items
     * @throws UnexpectedReturnValueException
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function createInvoice(array $items)
    {
        $invoiceParams = [
            'invoice' => [
                'id' => 0,
                'objectName' => 'Invoice',
                'taxType' => 'default',
                'currency' => 'EUR',
                'taxText' => 'zzgl. Umsatzsteuer 19%',
                'taxRate' => $this->sevdeskConfiguration->getTaxRate(),
                'contact' => [
                    'id' => $this->sevdeskConfiguration->getSevdeskContactId(),
                    'objectName' => 'contact',
                ],
                'contactPerson' => [
                    'id' => $this->sevdeskConfiguration->getSevdeskContactPersonId(),
                    'objectName' => 'sevUser',
                ],
                'smallSettlement' => false,
                'invoiceDate' => date('d.m.Y'),
                'status' => 100,
                'showNet' => true,
                'discount' => 0,
                'invoiceType' => 'RE',
                'invoiceNumber' => $this->getNextInvoiceNumber(),
                'deliveryDateUntil' => 0,
                'datevConnectOnline' => [],
                'sendPaymentReceivedNotificationDate' => 0,
                'mapAll' => true,
            ],
            'invoicePosSave' => $this->formatInvoicePos($items),
            'invoicePosDelete' => null,
            'discountSave' => null,
            'discountDelete' => null,
            'takeDefaultAddress' => true,
        ];

        try {
            $response = $this->client->request(
                'POST',
                'https://my.sevdesk.de/api/v1/Invoice/Factory/saveInvoice',
                [
                    'query' => [
                        'token' => $this->sevdeskConfiguration->getSevdeskApiKey(),
                    ],
                    'headers' => [
                        'Accept' => 'application/json'
                    ],
                    'json' => $invoiceParams
                ],
            );
        } catch (TransportExceptionInterface $e) {
            //HANDLING
        }
    }

    /**
     * @param ExportItemInterface[] $items
     */
    private function formatInvoicePos(array $items): array
    {
        $reArray = [];

        $i = 0;
        foreach ($items as $position) {

            $reArray[] = [
                'id' => $i,
                'objectName' => 'invoicePos',
                'quantity' => round($this->getDurationInHoursFromBeginAndEnd($position->getBegin(), $position->getEnd()), 2),
                'price' => $position->getHourlyRate() === null ? 0 : $position->getHourlyRate(),
                'name' => $this->eventNameBuilder($position),
                'unity' => [
                    'id' => $this->sevdeskConfiguration->getSevdeskHourUnitId(),
                    'objectName' => 'Unity'
                ],
                'text' => $this->eventTextBuilder($position),
                'taxRate' => $this->sevdeskConfiguration->getTaxRate(),
                'mapAll' => true,
            ];
            $i++;
        }

        return $reArray;
    }

    private function eventTextBuilder(ExportItemInterface $position): string
    {
        $text = $position->getDescription() === null ? '' : $position->getDescription();

        return $text .= "\nMitarbeiter: " . $position->getUser()->getDisplayName();
    }

    private function eventNameBuilder(ExportItemInterface $position): string
    {
        $name = $position->getBegin()->format('d.m.Y');

        if ($position->getActivity() !== null) {
            $name .= ' - ' . $position->getActivity()->getName();
        }

        return $name;
    }

    private function getDurationInHoursFromBeginAndEnd(DateTime $begin, DateTime $end): float
    {
        return $this->getMinutesDifference($begin, $end) / 60;
    }

    private function getMinutesDifference(DateTime $a, DateTime $b): int
    {
        return abs($a->getTimestamp() - $b->getTimestamp()) / 60;
    }
}