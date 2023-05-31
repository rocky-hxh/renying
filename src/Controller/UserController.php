<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Route('/users', name: 'users')]
class UserController extends AbstractController
{
    /**
     * @param UserRepository $users
     */
    public function __construct(private UserRepository $users)
    {
    }

    #[Route(path: "", name: "all", methods: ["GET"])]
    public function all(Request $request, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        $active = $request->query->get('active', null);
        $member = $request->query->get('member', null);
        $begin = $request->query->get('begin', null);
        $end = $request->query->get('end', null);
        $type = $request->query->get('type', null);

        $data = $this->users->findByCustom($active, $member, $begin, $end, $type);

        return $this->json($serializer->normalize($data));
    }
}
