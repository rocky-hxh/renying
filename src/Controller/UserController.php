<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/users', name: 'users')]
class UserController extends AbstractController
{
    /**
     * @param UserRepository $users
     */
    public function __construct(private UserRepository $users)
    {
    }

    /**
     * Filtered users via query parameters.
     *
     * Query parameters:
     *  active: ['null', '0', '1']; 'null' means parameter do not added or assigned
     *  member: ['null', '0', '1'];
     *  begin:  ['null', '<datetime>']
     *  end:    ['null', '<datetime>']
     *  type:   ['null', '1', '2', '3', '1,2', '1,3', '2,3', '1,2,3'];
     *
     * Examples:
     *  /users?active=0&member=0&begin=2020-01-07&end=2021-12-31&type=2,3
     *
     */
    #[Route(path: "", name: "all", methods: ["GET"])]
    public function filtered(Request $request, SerializerInterface $serializer): JsonResponse
    {
        try {
            $data = $this->users->findByCustom($request->query->all());
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }

        return $this->json([
            'data' => $serializer->normalize($data)
        ]);
    }
}
