<?php

namespace App\Fixtures;

use App\Event\Dispatcher\MessengerEventDispatcher;
use App\Factory\UuidFactory;
use App\Model\Pizza\Entity\Pizza\Event\CreatePizzaEvent;
use App\Model\Pizza\Entity\Pizza\Pizza;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PizzaFixtures extends Fixture
{

    public function __construct(private readonly MessengerEventDispatcher $dispatcher)
    {
    }

    private array $map = [
      'сыр',
      'колбаса',
      'томат',
      'базелик',
      'петрушка',
      'огурец',
      'вкусно',
      'великолепно'
    ];
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        for ($i = 0; $i < 100; $i++) {
            $uid = UuidFactory::generateNew();
            $name = $faker->unique()->word;
            $price = random_int(10,1000);
            $description = $this->generateDescription();

            $pizza = new Pizza($uid, $name, $price, $description);
            $manager->persist($pizza);
            $this->dispatcher->dispatch([new CreatePizzaEvent($uid, $name, $description, $price)]);
        }
        $manager->flush();
    }

    private function generateDescription(): string
    {
        $description = [];
        $blockedIndexes = [];

        while (count($description) < 3) {
            $mapIndex = random_int(0, count($this->map) - 1);
            if (!in_array($mapIndex, $blockedIndexes)) {
                $description[] = $this->map[$mapIndex];
                $blockedIndexes[] = $mapIndex;
            }
        }

        return implode(' ', $description);
    }
}