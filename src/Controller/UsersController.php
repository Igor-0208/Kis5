<?php


namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;

class UsersController extends AbstractController
{
    public function GetUsers()
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();
        if (!$users){
            return new Response("К сожалению нет ни одного пользователя:(");
        }
        foreach ($users as $user){
            var_dump($user);
            echo '<br/><br/>';
        }
        return new Response("Успешно!");
    }

    public function GetUserById($id)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        if (!$user) {
            return new Response('Пользователя с идентификатором '.$id.' не существует');
        }else{
            $userData = $this->json([
                'user' => [
                    [
                        "id"=> $id,
                        "name"=> $user->getName(),
                        "birthDate"=> $user->getBirthDate(),
                        "createdTime"=> $user->getCreatedTime(),
                        "updatedTime"=> $user->getUpdatedTime(),
                    ],
                ]
            ]);
        }
        return new Response('Пользователь: '.$userData->getContent());
    }

    public function PostUser(Request $request):Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();

        $user->setName($request->request->get('name'));
        $user->setBirthDate(new \DateTime($request->request->get('birthDate')));
        $user->setCreatedTime(new \DateTime('now'));
        $user->setUpdatedTime(new \DateTime('now'));

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
          $entityManager->persist($user);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved User with id '.$user->getId());
    }

    public function PutUser($id,Request $request):Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
        $isExist = true;
        if (!$user) {
            $user = new User();
            $isExist = false;
        }

        $user->setName($request->request->get('name'));
        $user->setBirthDate(new \DateTime($request->request->get('birthDate')));
        $user->setCreatedTime(new \DateTime('now'));
        $user->setUpdatedTime(new \DateTime('now'));

        $entityManager->persist($user);
        $entityManager->flush();

        if ($isExist) return new Response('Edited user with id '.$user->getId());
        return new Response('Existed new user with id '.$user->getId());
    }

    public function DeleteUser($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) return new Response('Пользователя с таким id нет :(');
        $entityManager->remove($user);
        $entityManager->flush();
        return new Response('Пользователь c индентификатором '.$id.' был уничтожен и стерт!');
    }
}