<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MovieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        
        $movie = new Movie();
        $movie->setTitle('The Dark Knight');
        $movie->setReleaseYear(2008);
        $movie->setDescription('This is a Description for the dark knight');
        $movie->setImagePath('https://cdn.pixabay.com/photo/2018/04/25/08/59/super-heroes-3349031_960_720.jpg');
        
        // Add Data To Pivot Table
        $movie->addActor($this->getReference('actor_1'));
        $movie->addActor($this->getReference('actor_2'));

        $manager->persist($movie);
        
        $movie2 = new Movie();
        $movie2->setTitle('Avengers:Endgame');
        $movie2->setReleaseYear(2019);
        $movie2->setDescription('This is a Description for the avengers endgame');
        $movie2->setImagePath('https://cdn.pixabay.com/photo/2019/05/26/01/13/avengers-4229465_960_720.jpg');
        
        // Add Data To Pivot Table
        $movie2->addActor($this->getReference('actor_3'));
        $movie2->addActor($this->getReference('actor_4'));
        
        $manager->persist($movie2);

        $manager->flush();
    }
}
