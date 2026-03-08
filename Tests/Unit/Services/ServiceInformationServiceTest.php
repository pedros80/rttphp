<?php

namespace Tests\Unit\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Pedros80\RTTphp\Exceptions\InvalidDateFormat;
use Pedros80\RTTphp\Exceptions\InvalidServiceIdFormat;
use Pedros80\RTTphp\Exceptions\InvalidServiceResponse;
use Pedros80\RTTphp\Exceptions\ServiceNotFound;
use Pedros80\RTTphp\Services\ServiceInformationService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class ServiceInformationServiceTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<Client> $client */
    private ObjectProphecy $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->prophesize(Client::class);
    }

    public function testSearchServiceHitsCorrectEndpoint(): void
    {
        $this->client->get('json/service/Y29995/2023/08/19')->shouldBeCalled()->willReturn(new Response(200, [], '{}'));

        $service = $this->makeServiceInformationService();
        $service->search('Y29995', '2023/08/19');
    }

    public function testSearchInvalidServiceIdThrowsException(): void
    {
        $this->expectException(InvalidServiceIdFormat::class);
        $this->expectExceptionMessage("'INVALID' is not a valid service id - [A-Z][0-9]{5}");

        $service = $this->makeServiceInformationService();
        $service->search('INVALID', '2023/08/19');
    }

    public function testSearchServiceWithInvalidDateThrowsException(): void
    {
        $this->expectException(InvalidDateFormat::class);
        $this->expectExceptionMessage("'2023-08-19' is not a valid date - yyyy/mm/dd");

        $service = $this->makeServiceInformationService();
        $service->search('Y29995', '2023-08-19');
    }

    public function testSearchUnknownStationThrowsException(): void
    {
        $this->expectException(ServiceNotFound::class);
        $this->expectExceptionMessage("Could not find service from 'json/service/Y29995/2023/08/19'. Please check url.");

        $this->client->get('json/service/Y29995/2023/08/19')->shouldBeCalled()->willReturn(new Response(404, [], '{}'));

        $service = $this->makeServiceInformationService();
        $service->search(
            serviceId: 'Y29995',
            date: '2023/08/19'
        );
    }

    public function testServiceReturnsInvalidJsonThrowsException(): void
    {
        $this->expectException(InvalidServiceResponse::class);
        $this->expectExceptionMessage('Invalid Service Response - could not decode to object');

        $this->client->get('json/service/Y29995/2023/08/19')->shouldBeCalled()->willReturn(new Response(200, [], 'invalid-json'));

        $service = $this->makeServiceInformationService();
        $service->search(
            serviceId: 'Y29995',
            date: '2023/08/19'
        );
    }

    public function testSearchThirdPartyErrorThrowsException(): void
    {
        $this->expectException(ServiceNotFound::class);
        $this->expectExceptionMessage("Could not find service from 'json/service/Y29995/2023/08/19'. Please check url.");

        $this->client->get('json/service/Y29995/2023/08/19')->shouldBeCalled()->willReturn(
            new Response(200, [], '{"error":"Unknown error"}')
        );

        $service = $this->makeServiceInformationService();
        $service->search(
            serviceId: 'Y29995',
            date: '2023/08/19'
        );
    }

    private function makeServiceInformationService(): ServiceInformationService
    {
        return new ServiceInformationService($this->client->reveal());
    }
}
