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
        // add french fixtures
        $faker = Faker\Factory::create("fr_FR");

        // add populator
        $populator = new \Faker\ORM\Doctrine\Populator($faker, $manager);

        // !USER
        //create admin user
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

        //create several users
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

        // !TAGS
        //create tags
        $populator->addEntity(Tag::class,15,[
            "name" => function () use ($faker) {
                return $faker->words(2, true);
            },
            "description" => function () use ($faker) {
                return $faker->text(100); 
            },
            "createdAt" => function () use ($faker) {
                return $faker->dateTime();
            }
        ]);

        // ! POST
        //creation of 15 posts with the faker
        $populator->addEntity(Post::class, 15, [
            'title'=> function() use ($faker) {
                return $faker->sentence(7);
            },
            'content'=> function() use ($faker) {
                return $faker->text(500);
            },
            'houlyRate'=>function() use ($faker) {
                return $faker->randomFloat(1, 1, 50);
            },
            'workType'=>function() use ($faker) {
                return $faker->boolean();
            },
            "postalCode" => function () use ($faker) {
                return $faker->numerify('#####');
            },
            'radius'=> function () use ($faker) {
                return $faker->numberBetween(0, 50);
            },
            "createdAt" => function () use ($faker) {
                return $faker->dateTime();
            },
        ]);

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
            
        // ! POST TAG
        $insertedItems = $populator->execute();

        // Creating posts array
        $posts = [];

        // putting posts in post array, with the help of $insertedItems variable
        foreach($insertedItems["App\Entity\Post"] as $post){
            // construct calling for some obscur reason
            $post->__construct();
            $posts[] = $post;
        }

        // Iterating on tags and adding randomly to each tag a post
        foreach($insertedItems["App\Entity\Tag"] as $tag){
            // construct calling for some obscur reason 
            $tag->__construct();

            // Get randomly generated index
            $randIndex = array_rand($posts);
            // adding this post to a tag
            $tag->addPost($posts[$randIndex]);


        $manager->flush();
    }
}
}

