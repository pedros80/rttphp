<?php

namespace Tests\Unit\Factories;

use Pedros80\RTTphp\Factories\ServicesFactory;
use Pedros80\RTTphp\Services\LocationService;
use Pedros80\RTTphp\Services\ServiceInformationService;
use PHPUnit\Framework\TestCase;

final class ServicesFactoryTest extends TestCase
{
    public function testFactoryCanMakeLocationService(): void
    {
        $factory = new ServicesFactory();

        $service = $factory->makeLocationService('user', 'pass');

        $this->assertInstanceOf(LocationService::class, $service);
    }

    public function testFactoryCanMakeServiceInformationService(): void
    {
        $factory = new ServicesFactory();

        $service = $factory->makeServiceInformationService('user', 'pass');

        $this->assertInstanceOf(ServiceInformationService::class, $service);
    }
}
