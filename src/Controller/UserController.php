<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
     * Filtered users.
     *
     * Filter Options:
     *  active: ['null', '0', '1']; 'null' mean option do not added or assigned
     *  member: ['null', '0', '1'];
     *  begin:  ['null', '']
     *  end:    ['null', '']
     *  type:   ['null', '1', '2', '3', '1,2', '1,3', '2,3', '1,2,3'];
     *
     * Examples:
     *  /users?active=0&member=0&begin=2020-01-07&end=2021-12-31&type=2,3
     *
     */
    #[Route(path: "", name: "all", methods: ["GET"])]
    public function all(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'active' => null,
            'member' => null,
            'begin'  => null,
            'end'    => null,
            'type'   => null,
        ]);
        $resolver->setAllowedTypes('active', ['null', 'string']);
        $resolver->setAllowedValues('active', [null, '0', '1']);
        $resolver->setAllowedTypes('member', ['null', 'string']);
        $resolver->setAllowedValues('member', [null, '0', '1']);
        $resolver->setAllowedTypes('begin', ['null', 'string']);
        $resolver->setAllowedValues('begin', function ($value) {
            try {
                $t = new \DateTimeImmutable($value);
            } catch (\Exception $e) {
                return false;
            }
            return true;
        });
        $resolver->setAllowedValues('end', function ($value) {
            try {
                $t = new \DateTimeImmutable($value);
            } catch (\Exception $e) {
                return false;
            }
            return true;
        });
        $resolver->setAllowedTypes('type', ['null', 'string']);
        $resolver->setAllowedValues('type', function ($value) {
            $allows = ['1', '2', '3'];
            $inputs = explode(',', $value);
            $union = array_unique(array_merge($allows, $inputs));
            return count($allows) == count($union);
        });

        try {
            $options = $resolver->resolve($request->query->all());
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }

        $data = $this->users->findByCustom(...$options);
        return $this->json([
            'data' => $serializer->normalize($data)
        ]);
    }
}
