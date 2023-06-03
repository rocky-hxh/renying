<?php
namespace App\Tests\Repository;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class UserRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testFindByCustom()
    {
        $users = $this->entityManager
            ->getRepository(User::class)
            ->findByCustom(['active' => '1', 'type' => '3'])
        ;
        $this->assertSame(2, count($users));

        // curl -s "http://homestead.test/users?active=0" | jq '.data | length'
        $users = $this->entityManager
            ->getRepository(User::class)
            ->findByCustom(['active' => '0'])
        ;
        $this->assertSame(344, count($users));
    }

    public function testFindByCustomException()
    {
        $this->expectException(InvalidOptionsException::class);
        $users = $this->entityManager
            ->getRepository(User::class)
            ->findByCustom(['active' => '1', 'type' => '1,'])
        ;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}