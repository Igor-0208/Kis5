<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Post;

class PostsController extends AbstractController
{
    public function GetPosts()
    {
        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findAll();
        if (!$posts){
            return new Response("К сожалению нет ни одного поста :(");
        }
        foreach ($posts as $post){
            echo $post->getId();
            echo '<br/>';
            $author = $post->getAuthor();
            echo $author->getName();
            echo '<br/>';
            echo $post->getTitle();
            echo '<br/>';
            echo $post->getText();
            echo '<br/>';
            echo $post->getLikes();
            echo '<br/>';
            echo $post->getViews();
            echo '<br/><br/>';
        }
        return new Response("Успешно!");
    }

    public function GetPost($id)
    {
        $post = $this->getDoctrine()
            ->getRepository(Post::class)
            ->find($id);

        if (!$post) {
            return new Response('Поcта с идентификатором '.$id.' не существует');
        }else{
            var_dump($post->getText());//не понимаю почему но в юзер контроллере работает здесь нет(((
            $post = $this->json([
                'post' => [
                    [
                        "id"=> $id,
                        "title"=> $post->getTitle(),
                        "text"=>  $post->getText(),
                        "likes"=> $post->getLikes(),
                        "views"=> $post->getViews(),
                        "createdTime"=> $post->getPostedTime(),
                        "updatedTime"=> $post->getUpdateTime(),

                    ],
                ]
            ]);
        }
        return new Response('Пост: '. $post->getContent());
    }

    public function PostPost(Request $request):Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $post = new Post();
        $author = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($request->request->get('authorId'));
        $post->setAuthor($author);
        $post->setTitle($request->request->get('postTitle'));
        $post->setText($request->request->get('postText'));
        $post->setTags([$request->request->get('tags')]);
        $post->setLikes(0);
        $post->setViews(0);
        $post->setPostedTime(new \DateTime('now'));
        $post->setUpdateTime(new \DateTime('now'));

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($post);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new plan with id '.$post->getId());
    }

    public function PutPost($id,Request $request):Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository(Post::class)->find($id);
        $isExist = true;
        if (!$post) {
            $post = new Post();
            $isExist = false;
        }

        $author = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($request->request->get('authorId'));
        $post->setAuthor($author);
        $post->setTitle($request->request->get('postTitle'));
        $post->setText($request->request->get('postText'));
        $post->setTags([$request->request->get('tags')]);
        $post->setLikes(0);
        $post->setViews(0);
        $post->setPostedTime(new \DateTime('now'));
        $post->setUpdateTime(new \DateTime('now'));

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($post);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        if ($isExist) return new Response('Edited post with id '.$post->getId());
        return new Response('Existed new post with id '.$post->getId());
    }

    public function DeletePost($id):Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository(Post::class)->find($id);
        if (!$post) return new Response('Поста с таким id нет :(');
        $entityManager->remove($post);
        $entityManager->flush();
        return new Response('Пост c индентификатором '.$id.' был уничтожен и стерт!');
    }
}
