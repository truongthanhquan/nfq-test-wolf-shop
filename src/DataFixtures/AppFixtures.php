<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\ItemEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $itemsData = [
            'Apple AirPods' => [
                'sellIn' => 10,
                'quality' => 20,
                'attributes' => [
                    'color' => 'WHITE',
                ],
                'image' => [
                    'url' => 'https://example.com/airpods.jpg',
                ],
            ],
            'Apple iPad Air' => [
                'sellIn' => 10,
                'quality' => 20,
                'attributes' => [
                    'color' => 'BLACK',
                ],
                'image' => [
                    'url' => 'https://example.com/ipad.jpg',
                ],
            ],
            'Samsung Galaxy S23' => [
                'sellIn' => 10,
                'quality' => 20,
                'attributes' => [
                    'color' => 'RED',
                ],
                'image' => [
                    'url' => 'https://example.com/samsung.jpg',
                ],
            ],
            'Xiaomi Redmi Note 13' => [
                'sellIn' => 10,
                'quality' => 20,
                'attributes' => [
                    'color' => 'YELLOW',
                ],
                'image' => [
                    'url' => 'https://example.com/xiaomi.jpg',
                ],
            ],
            'Huawei Mate XT' => [
                'sellIn' => 10,
                'quality' => 20,
                'attributes' => [
                    'color' => 'BLUE',
                ],
                'image' => [
                    'url' => 'https://example.com/huawei.jpg',
                ],
            ],
        ];
        foreach ($itemsData as $name => $data) {
            $item = new ItemEntity();
            $item->setName($name);
            foreach ($data as $property => $value) {
                $setter = 'set' . ucfirst($property);
                if (method_exists($item, $setter)) {
                    $item->{$setter}($value);
                }
            }
            $manager->persist($item);
        }
        $manager->flush();
    }
}
