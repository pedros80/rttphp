<?php

namespace Tests\Unit\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Pedros80\RTTphp\Exceptions\InvalidDateFormat;
use Pedros80\RTTphp\Exceptions\InvalidServiceIdFormat;
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
        $client->get('json/service/Y29995/2023/08/19')->shouldBeCalled()->willReturn(new Response(200, [], '{}'));
        $service = new ServiceInformationService($client->reveal());
        $service->search('Y29995', '2023/08/19');
    }

    public function testSearchInvalidServiceIdThrowsException(): void
    {
        $this->expectException(InvalidServiceIdFormat::class);
        $this->expectExceptionMessage("'INVALID' is not a valid service id - [A-Z][0-9]{5}");

        $service = new ServiceInformationService($this->prophesize(Client::class)->reveal());
        $service->search('INVALID', '2023/08/19');
    }

    public function testSearchServiceWithInvalidDateThrowsException(): void
    {
        $this->expectException(InvalidDateFormat::class);
        $this->expectExceptionMessage("'2023-08-19' is not a valid date - yyyy/mm/dd");

        $service = new ServiceInformationService($this->prophesize(Client::class)->reveal());
        $service->search('Y29995', '2023-08-19');
    }

    public function testSearchUnknownStationThrowsException(): void
    {
        $this->expectException(ServiceNotFound::class);
        $this->expectExceptionMessage("Could not find service from 'json/service/Y29995/2023/08/19'. Please check url.");

        $client = $this->prophesize(Client::class);
        $client->get('json/service/Y29995/2023/08/19')->shouldBeCalled()->willReturn(new Response(404, [], '{}'));
        $service = new ServiceInformationService($client->reveal());
        $service->search(
            serviceId: 'Y29995',
            date: '2023/08/19'
        );
    }

    public function testSearchThirdPartyErrorThrowsException(): void
    {
        $this->expectException(ServiceNotFound::class);
        $this->expectExceptionMessage("Could not find service from 'json/service/Y29995/2023/08/19'. Please check url.");

        $client = $this->prophesize(Client::class);
        $client->get('json/service/Y29995/2023/08/19')->shouldBeCalled()->willReturn(
            new Response(200, [], '{"error":"Unknown error"}')
        );
        $service = new ServiceInformationService($client->reveal());
        $service->search(
            serviceId: 'Y29995',
            date: '2023/08/19'
        );
    }
}
