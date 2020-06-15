<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentsController extends AbstractController
{
    public function GetComments($postID)
    {
        $comments = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findALL($postID);
        if (!$comments){
            return new Response("К сожалению нет ни одного комента :(");
        }

        foreach ($comments as $comment){
            echo $comment->getId();
            echo '<br/>';
            $author = $comment->getAuthor();
            echo $author->getName();
            echo '<br/>';
            echo $comment->getText();
            echo '<br/>';
            echo $comment->getLikes();
            echo '<br/><br/>';

        }
        return new Response("Успешно!");
    }

    public function CreateComment($postID, Request $request):Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        $comment = new Comment();
        $post = $this->getDoctrine()
            ->getRepository(Post::class)
            ->find($postID);

        $comment->setPost($post);

        $author = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($request->request->get('authorId'));
        $comment->setAuthor($author);
        $comment->setText($request->request->get('commentText'));
        $comment->setLikes('0');
        $comment->setPostedTime(new \DateTime('now'));
        $comment->setUpdatedTime(new \DateTime('now'));

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($comment);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved Comment with id '.$comment->getId());
    }

    public function GetComment($id)
    {
        $comment = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->find($id);

        return new Response("Успешно! Найден комментарий с id: ". $comment->getText());
    }

    public function PutComment($id,Request $request):Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $comment = $entityManager->getRepository(Comment::class)->find($id);
        $isExist = true;
        if (!$comment) {
            $comment = new Comment();
            $isExist = false;
        }

        $post = $this->getDoctrine()
            ->getRepository(Post::class)
            ->find($request->request->get('postId'));

        $comment->setPost($post);

        $author = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($request->request->get('authorId'));
        $comment->setAuthor($author);
        $comment->setText($request->request->get('commentText'));
        if(!$isExist){
            $comment->setLikes('0');
            $comment->setPostedTime(new \DateTime('now'));
        }
        $comment->setUpdatedTime(new \DateTime('now'));

        $entityManager->persist($comment);
        $entityManager->flush();

        if ($isExist) return new Response('Edited comment with id '. $comment->getId() . $comment->getText());
        return new Response('Existed new comment with id '. $comment->getId() . $comment->getText());
    }

    public function DeleteComment($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $comment = $entityManager->getRepository(Comment::class)->find($id);
        if (!$comment) return new Response('Комментария с таким id нет :(');
        $entityManager->remove($comment);
        $entityManager->flush();
        return new Response('Коммент c индентификатором '.$id.' был уничтожен и стерт!');
    }
}
