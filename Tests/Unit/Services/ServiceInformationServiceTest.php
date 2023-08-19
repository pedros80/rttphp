<?php

namespace Tests\Unit\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Pedros80\RTTphp\Exceptions\InvalidDateFormat;
use Pedros80\RTTphp\Exceptions\ServiceNotFound;
use Pedros80\RTTphp\Services\ServiceInformationService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class ServiceInformationServiceTest extends TestCase
{
    use ProphecyTrait;

    public function testSearchServiceHitsCorrectEndpoint(): void
    {
        $client = $this->prophesize(Client::class);
        $client->get('json/service/serviceId/2023/08/19')->shouldBeCalled()->willReturn(new Response(200, [], '{}'));
        $service = new ServiceInformationService($client->reveal());
        $service->search('serviceId', '2023/08/19');
    }

    public function testSearchServiceWithInvalidDateThrowsException(): void
    {
        $this->expectException(InvalidDateFormat::class);
        $this->expectExceptionMessage("'2023-08-19' is not a valid date - yyyy/mm/dd");

        $service = new ServiceInformationService($this->prophesize(Client::class)->reveal());
        $service->search('serviceId', '2023-08-19');
    }

    public function testSearchUnknownStationThrowsException(): void
    {
        $this->expectException(ServiceNotFound::class);
        $this->expectExceptionMessage("Could not find service from 'json/service/XXXXXX/2023/08/19'. Please check url.");

        $client = $this->prophesize(Client::class);
        $client->get('json/service/XXXXXX/2023/08/19')->shouldBeCalled()->willThrow(
            new ClientException(
                'error message',
                new Request('get', 'json/service/XXXXXX/2023/08/12'),
                new Response(404, [], '{}')
            )
        );
        $service = new ServiceInformationService($client->reveal());
        $service->search(
            serviceId: 'XXXXXX',
            date: '2023/08/19'
        );
    }
}
