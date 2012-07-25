<?php
namespace Esimed\CarLocation\BackendBundle\DataFixtures\ORM\Data;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Esimed\CarLocation\BackendBundle\Entity as Entity;

class Data implements FixtureInterface, ContainerAwareInterface {

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager) {

        //load default agence
        $agence = new Entity\Agence();
        $agence
            ->setNom("la Valentine")
            ->setVille("Marseille");

        $manager->persist($agence);
        $manager->flush();


        //load default user
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->createUser();

        $user
            ->setUsername('admin')
            ->setEmail("admin@carLocation.com")
            ->setPlainPassword('test')
            ->setAgence($agence)
            ->setEnabled(true);
        $userManager->updateUser($user);
        $manager->flush();


        $tourisme = Entity\Categorie::$TYPE_TOURISME;
        $utilitaire =  Entity\Categorie::$TYPE_UTILITAIRE;

        $periodes = array(
            $tourisme => array(
                '1 jour', '1 jour', '1 jour', '5 jours', '7 jours',  'samedi+dimanche', 'Week-end'
            ),
            $utilitaire => array(
                '1 jour', '1 jour', 'Samedi', 'Week-end', '5 jours'
            )
        );

        $kms = array(
            $tourisme => array(
                100, 250, 400, 1250, 1500, 500, 800
            ),
            $utilitaire => array(
                100 , 500 , 100 , 500 , 200 , 1000 , 1000
            )
        );


        //create category and price
        $categories = array(
            array('type' => $tourisme, 'nom' => 'catégorie 1',
                'prix' => array(45, 55, 65, 199, 239, 89, 99)),
            array('type' => $tourisme, 'nom' => 'catégorie 2',
                'prix' => array(49, 59, 75, 219, 249, 99, 109)),
            array('type' => $tourisme, 'nom' => 'catégorie 3',
                'prix' => array(59, 69, 85, 259, 269, 109, 129)),
            array('type' => $tourisme, 'nom' => 'catégorie 4',
                'prix' => array( 65, 79, 99, 299, 329, 129, 149)),
            array('type' => $tourisme, 'nom' => 'catégorie 5',
                'prix' => array(79, 99, 119, 369, 419, 159, 179)),
            array('type' => $tourisme, 'nom' => 'catégorie 6',
                'prix' => array(99, 119, 149, 449, 559, 199, 239)),
            array('type' => $utilitaire, 'nom' => 'catégorie A',
                'prix' => array(43, 79, 59, 109, 99, 185, 229)),
            array('type' => $utilitaire, 'nom' => 'catégorie A\'',
                'prix' => array(55, 99, 75, 119, 129, 219, 289)),
            array('type' => $utilitaire, 'nom' => 'catégorie B',
                'prix' => array(59, 109, 89, 155, 149, 265, 319)),
            array('type' => $utilitaire, 'nom' => 'catégorie C',
                'prix' => array(69, 129, 95, 159, 159, 289, 329)),
            array('type' => $utilitaire, 'nom' => 'catégorie D',
                'prix' => array(79, 145, 109, 175, 189, 319, 349)),
            array('type' => $utilitaire, 'nom' => 'catégorie E',
                'prix' => array(99, 179, 135, 209, 229, 389, 439))
        );
        foreach ($categories as $data) {

            //create categorie
            $categorie = new Entity\Categorie();
            $categorie
                ->setType($data['type'])
                ->setNom($data['nom']);
            $manager->persist($categorie);
            $manager->flush();

            $prix = $data['prix'];

            //create associated forfait
            for ($i=0; $i < count($periodes[$categorie->getType()]); $i++) {

                $forfait = new Entity\Forfait();
                $forfait
                    ->setCategorie($categorie)
                    ->setKmMax($kms[$categorie->getType()][$i])
                    ->setPeriode($periodes[$categorie->getType()][$i])
                    ->setPrix($prix[$i]);

                $manager->persist($forfait);
                $manager->flush();
            }
        }

    }
}

