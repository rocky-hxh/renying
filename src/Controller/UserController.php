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
        $resolver->setAllowedValues('active', ['0', '1']);
        $resolver->setNormalizer('active', function (Options $options, $value) {
            return $value;
        });
        $resolver->setAllowedTypes('member', ['null', 'string']);
        $resolver->setAllowedValues('member', ['0', '1']);

        try {
            $options = $resolver->resolve($request->query->all());
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage(),
                'data' => [],
            ]);
        }

        $data = $this->users->findByCustom(...$options);
        return $this->json([
            'data' => $serializer->normalize($data)
        ]);
    }
}
