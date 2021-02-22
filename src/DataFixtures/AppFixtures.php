<?php

namespace App\DataFixtures;

use App\Entity\Reponse;
use App\Entity\Topic;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $userArray = [];

        $charlesUser = new User();
        $charlesUser->setEmail('charles-user@gmail.com');
        $charlesUser->setRoles(['ROLE_USER']);
        $charlesUser->setPassword($this->encoder->encodePassword($charlesUser, 'charles-user'));
        $manager->persist($charlesUser);

        $userArray[] = $charlesUser;

        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail('user' . $i .'@gmail.com');
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->encoder->encodePassword($user, 'user-' . $i));
            $manager->persist($user);

            $userArray[] = $user;
        }

        for ($i = 1; $i <= 25; $i++) {
            $topic = new Topic();
            $topic->setTitre('Titre ' . $i);
            $topic->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
            $topic->setUser($userArray[array_rand($userArray)]);
            $topic->setCreatedAt(new DateTime('2021-02-' . $i . ' 08:' . $i . ':00'));
            $topic->setUpdatedAt(new DateTime('2021-03-' . $i . ' 08:' . $i . ':00'));
            $manager->persist($topic);

            for ($j = 1; $j <= 5; $j++) {
                $reponse = new Reponse();
                $reponse->setMessage('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
                $reponse->setUser($userArray[array_rand($userArray)]);
                $reponse->setCreatedAt(new DateTime('2021-04-' . $i . ' 08:' . $i . ':00'));
                $reponse->setTopic($topic);
                $manager->persist($reponse);
            }
        }

        $manager->flush();
    }
}
