<?php

namespace App\Tests\Controller\Api;

use App\Entity\Post;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    private $client;
    private $user;
    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    private function logIn()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'jean.olivie@club-internet.fr']);
        $this->user = $user;
        $token = static::getContainer()->get('lexik_jwt_authentication.jwt_manager')->create($user);
        
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));

        $this->client->loginUser($user);
    }

    public function testEditPost(): void
    {

      
        $this->logIn();

       


         // create a test post
        $post = new Post();
        $post->setTitle('Test Post');
        $post->setContent('This is a test post.');
        $post->setHourlyRate(33);
        $post->setWorkType(false);
        $post->setPostalCode(33000);
        $post->setSlug($post->getSlug());
        $post->setRadius(3);
        $post->setUser($this->user);
        
       


         // save the test post
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($post);
        $entityManager->flush();
        
        
        // edit the test post
        $postData = [
            'title' => 'Test Post Updated',
            'content' => 'This is an updated test post.This is an updated test post.This is an updated test post.This is an updated test post.This is an updated test post.This is an updated test post.This is an updated test post.',
            "hourlyRate"=>35,
            "workType"=> true,
            "postalCode"=>"74555",
            "radius"=>5,
            "tag"=>[1, 2]
            ];
        $this->client->request(
            'POST',
            '/api/annonce/'.$post->getId().'/modifier',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($postData)
        );
        
        // assert response
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('application/json', $this->client->getResponse()->headers->get('Content-Type'));
        
        // assert post was updated

        /** @var Post $updatedPost */ 
      
        $updatedPost = $entityManager->getRepository(Post::class)->find($post->getId());
       
        $this->assertEquals($postData['title'], $updatedPost->getTitle());
        $this->assertEquals($postData['content'], $updatedPost->getContent());
        $this->assertEquals($postData['hourlyRate'], $updatedPost->getHourlyRate());
        $this->assertEquals($postData['workType'], $updatedPost->isWorkType());
        $this->assertEquals($postData['postalCode'], $updatedPost->getPostalCode());
        $this->assertEquals($postData['radius'], $updatedPost->getRadius());
        $data=[]; 
        foreach($updatedPost->getTag() as $tag){
            $data[]=$tag->getId();
        }
        
        $this->assertEquals($postData['tag'], $data);
    }
}
