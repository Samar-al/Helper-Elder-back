<?php

namespace App\Tests\Controller\Api;

use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function testEditPost(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        
        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('ufournier@fouquet.com');

         // simulate $testUser being logged in
         $client->loginUser($testUser);
         $id = 552;
         $crawler = $client->request('POST', '/api/annonce/'.$id.'/modifier');

       // $post = $this->createPost($testUser); 

        $data = [
            'title' => 'Updated post title',
            'content' => 'Updated post content',
        ];
        $json = json_encode($data);
        
        $client->request('POST', '/api/annonce/'.$post->getId().'/modifier', [], [], [], $json);

        // Check if the response is successful
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        // Check if the post data has been updated
        $entityManager = $this->getEntityManager();
        $entityManager->refresh($post);
        $this->assertSame($data['title'], $post->getTitle());
        $this->assertSame($data['content'], $post->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Hello World');
    }
}
