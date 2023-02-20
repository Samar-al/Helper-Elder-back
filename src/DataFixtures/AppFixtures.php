<?php

namespace App\DataFixtures;

use App\Entity\Review;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;


class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // ajout des fixtures en français
        $faker = Faker\Factory::create("fr_FR");

        //ajout du populator
        $populator = new \Faker\ORM\Doctrine\Populator($faker, $manager);

        // !USER
        //creation d'un admin
        $userAdmin = new User();
        $userAdmin->setEmail("admin@admin.com");
        $userAdmin->setFirstname("admin");
        $userAdmin->setLastname("admin");
        $userAdmin->setBirthdate(new DateTime("1998-09-20"));
        $userAdmin->setGender(1);
        $userAdmin->setPostalCode("60320");
        $userAdmin->setPassword($this->passwordHasher->hashPassword($userAdmin, "admin"));
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setDescription("Je suis le super admin! wwwwwwwwwwwwooooooooooooouuuuuuuhhhhhhhhhhhhhoooooooooooooooouuuuuuuuuuuuuuuuuu");
        $userAdmin->setCreatedAt(new DateTime("now"));
        $manager->persist($userAdmin);

        //Création de plusieurs utilisateur
        $populator->addEntity(User::class,10,[
            "firstname" => function () use ($faker) {
                return $faker->firstName(10, 240);
            },
            "lastname" => function () use ($faker) {
                return $faker->lastName(10, 240);
            },
            "email" => function () use ($faker) {
                return $faker->unique()->email();
            },
            "birthdate" => function () use ($faker) {
                return $faker->dateTime();
            },
            "gender" => function () use ($faker) {
                return $faker->numberBetween(1,2);
            },
            "postalCode" => function () use ($faker) {
                return $faker->numerify('#####');
            },
            "description" => function () use ($faker) {
                return $faker->text(500);
            },
            "createdAt" => function () use ($faker) {
                return $faker->dateTime();
            }
        ]);

            $user = new User();
            $user->setPassword($this->passwordHasher->hashPassword($user, 'user'));
            $user->setRoles(["ROLE_USER"]);
            $manager->persist($user);

        // ! Review
            $populator->addEntity(Review::class,10,[
            "content" => function () use ($faker) {
                return $faker->text(300);
            },
            "rate" => function() use ($faker) {
                return $faker->randomFloat(1, 1, 5);
            },
            "createdAt" => function () use ($faker) {
                return $faker->dateTime();
            },
        ]);
            

        $manager->flush();
    }
}
