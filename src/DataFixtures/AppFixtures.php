<?php

namespace App\DataFixtures;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Post;
use App\Entity\Review;
use App\Entity\Tag;
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
        $userAdmin->setType(3);
        $manager->persist($userAdmin);

        $userAnonyme = new User();
        $userAnonyme->setEmail("anonyme@anonyme.com");
        $userAnonyme->setFirstname("anonyme");
        $userAnonyme->setLastname("anonyme");
        $userAnonyme->setBirthdate(new DateTime("1998-09-20"));
        $userAnonyme->setGender(1);
        $userAnonyme->setPostalCode("00000");
        $userAnonyme->setPassword($this->passwordHasher->hashPassword($userAnonyme, "user"));
        $userAnonyme->setRoles(["ROLE_USER"]);
        $userAnonyme->setDescription("Je suis anonyme! wwwwwwwwwwwwooooooooooooouuuuuuuhhhhhhhhhhhhhoooooooooooooooouuuuuuuuuuuuuuuuuu");
        $userAnonyme->setCreatedAt(new DateTime("now"));
        $userAnonyme->setType(1);
        $manager->persist($userAnonyme);

 
        //create several users
        $populator->addEntity(User::class, 10, [
            "email" => function () use ($faker) {
                return $faker->unique()->email();
            },
            "firstname" => function () use ($faker) {
                return $faker->firstName(10, 240);
            },
            "lastname" => function () use ($faker) {
                return $faker->lastName(10, 240);
            },
            "birthdate" => function () use ($faker) {
                return $faker->dateTime();
            },
            "gender" => function () use ($faker) {
                return $faker->numberBetween(1, 2);
            },
            "postalCode" => function () use ($faker) {
                return $faker->numerify('#####');
            },
            "description" => function () use ($faker) {
                return $faker->text(500);
            },
            "picture" => function () use ($faker) {
                return "https://picsum.photos/id/" . $faker->numberBetween(1, 200) . "/100/100";
            },
            "avgRating" => function () use ($faker) {
                return $faker->randomFloat(1, 1, 5);
            },
            "createdAt" => function () use ($faker) {
                return $faker->dateTime();
            },
            "updatedAt" => function () {
                return null;
            },
            "type" => function () use ($faker) {
                return $faker->numberBetween(1, 2);
            },
        ]);
        
        // !TAG
        //create tags
        $tagsName = ["Actes médicaux", "Chauffeur", "Compagnie", "Courses", "Cuisine", "Ménage", "Toilette"];
        $tagsLogo = ["img/actes-medicaux.png", "img/chauffeur.png", "img/compagnie.png", "img/courses.png", "img/cuisine.png", "img/ménage.png", "img/toilette.png"];
        $populator->addEntity(Tag::class, 7, [
            "name" => function () use ($tagsName) {
                static $index = 0;
                return $tagsName[$index++];
            },
            "description" => function () use ($faker) {
                return $faker->text(100);
            },
            "logo" => function () use ($tagsLogo) {
                static $index = 0;
                return $tagsLogo[$index++];
            },
            "createdAt" => function () use ($faker) {
                return $faker->dateTime('now');
            },
            "updatedAt" => function () use ($faker) {
                return null;
            }
        ]);

        // ! POST
        //creation of 15 posts with the faker
        $populator->addEntity(Post::class, 15, [
            'title'=> function () use ($faker) {
                return $faker->sentence(7);
            },
            'content'=> function () use ($faker) {
                return $faker->text(500);
            },
            'hourlyRate'=>function () use ($faker) {
                return $faker->randomFloat(1, 1, 50);
            },
            'workType'=>function () use ($faker) {
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
            "updatedAt" => function () use ($faker) {
                return null;
            }

        ]);
        
        
        // ! Review
        $populator->addEntity(Review::class, 10, [
            "content" => function () use ($faker) {
                return $faker->text(300);
            },
            "rate" => function () use ($faker) {
                return $faker->randomFloat(1, 1, 5);
            },
            "createdAt" => function () use ($faker) {
                return $faker->dateTime();
            },
            "updatedAt" => function () use ($faker) {
                return null;
            }
        ]);

        // ! Conversation
        $populator->addEntity(Conversation::class, 10, [
            "title" => function () use ($faker) {
                return $faker->text(150);
            },
            "createdAt" => function () use ($faker) {
                return $faker->dateTime();
            },
            "updatedAt" => function () use ($faker) {
                return null;
            },
            
        ]);

        // ! Message
        $populator->addEntity(Message::class, 10, [
            "content" => function () use ($faker) {
                return $faker->text(150);
            },
            "createdAt" => function () use ($faker) {
                return $faker->dateTime();
            },
            "updatedAt" => function () use ($faker) {
                return null;
            },
            "readByUser"=>function () use ($faker) {
                return $faker->boolean();
            },

        ]);

        // ! POST TAG
        $insertedItems = $populator->execute();
        
        // Creating posts array
        $posts = [];

        // putting posts in post array, with the help of $insertedItems variable
        foreach ($insertedItems["App\Entity\Post"] as $post) {
            // construct calling for some obscur reason
            $post->__construct();
            $posts[] = $post;
            $post->setSlug($post->getSlug());
        }

        // Iterating on tags and adding randomly to each tag a post
        foreach ($insertedItems["App\Entity\Tag"] as $tag) {
            // construct calling for some obscur reason
            $tag->__construct();

            // Get randomly generated index
            $randIndex = array_rand($posts);
            // adding this post to a tag
            $tag->addPost($posts[$randIndex]);
        }

        foreach($insertedItems["App\Entity\User"] as $user){
            // J'appelle le constructeur car pour une raison très sombre il n'est pas appellé 
            $user->__construct();
            $user->setPassword($this->passwordHasher->hashPassword($user, 'user'));
            $user->setRoles(["ROLE_USER"]);
            $manager->persist($user);       
        }
         // Creating messages array
         $conversations = [];

         // putting posts in post array, with the help of $insertedItems variable
         foreach ($insertedItems["App\Entity\Conversation"] as $conversation) {
             // construct calling for some obscur reason
             $conversation->__construct();
             $conversations[] = $conversation;
         }
         foreach ($insertedItems["App\Entity\Message"] as $message) {
             // construct calling for some obscur reason
            
 
             // Get randomly generated index
             $randIndex = array_rand($conversations);
             // adding this post to a tag
             $message->setConversation($conversations[$randIndex]);
         }
        
         // Creating messages array
         $messages = [];

         // putting posts in post array, with the help of $insertedItems variable
         foreach ($insertedItems["App\Entity\Message"] as $message) {
             // construct calling for some obscur reason
             
             $messages[] = $message;
         }
         // Iterating on user and adding randomly to each user a message
        foreach ($insertedItems["App\Entity\User"] as $user) {
            // construct calling for some obscur reason
            $user->__construct();

            // Get randomly generated index
            $randIndex = array_rand($messages);
            // adding this post to a tag
            $user->addMessagesSender($messages[$randIndex]);
        }
/* 
         // Creating messages array
        $reviews = [];

        // putting posts in post array, with the help of $insertedItems variable
        foreach ($insertedItems["App\Entity\Reviews"] as $review) {
            // construct calling for some obscur reason
            
            $reviews[] = $review;
        }
        // Iterating on user and adding randomly to each user a message
       foreach ($insertedItems["App\Entity\User"] as $user) {
           // construct calling for some obscur reason
           $user->__construct();

           // Get randomly generated index
           $randIndex = array_rand($reviews);
           // adding this post to a tag
           $user->addReviewsTaker($reviews[$randIndex]);
       }
 */
           
       
        $manager->flush();
    }
}
