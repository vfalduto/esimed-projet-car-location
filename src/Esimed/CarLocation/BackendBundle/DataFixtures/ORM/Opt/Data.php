<?php
namespace Esimed\CarLocation\BackendBundle\DataFixtures\ORM\Opt;

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

        $agences = array();

        //agence
        foreach (array("paris", "nantes", "momtpelier", "sanary", "manosque", "aix-en-provence",
                     "toulouse", "toulon", "gap") as $ville) {
            $agence = new Entity\Agence();
            $agence
                ->setNom("Agence " . ucfirst($ville))
                ->setVille($ville);

            $manager->persist($agence);
            $manager->flush();

            $agences[] = $agence;


            //load agent
            $userManager = $this->container->get('fos_user.user_manager');
            $user = $userManager->createUser();

            $user
                ->setUsername('agent' . $ville)
                ->setEmail( $ville . "@carLocation.com")
                ->setPlainPassword('1234')
                ->setAgence($agence)
                ->setEnabled(true);
            $userManager->updateUser($user);
            $manager->flush();
        }

        //véhicules
        $marques = array(
            "Peugueot" => array("206", "306", "406"),
            "Renault" => array("megane", "'versatis", "megane 3"),
            "Ford" => array("escort", "GT", "T"),
            "Toyota" => array("Yaris", "Prius", "Yaris"),
        );

        foreach ($marques as $marque => $modele) {

            $age = rand(18, 30);
            $cat = $manager->getRepository("EsimedCarLocationBackendBundle:Categorie")->findAll();
            $bt = array("Automatique", "Manuelle");
            $m = array("Essence", "Diesel");


            for($i = 0; $i <3; $i++) {
                $agence = $agences[rand(0, 8)];

                $voiture = new \Esimed\CarLocation\BackendBundle\Entity\Voiture();
                $voiture
                    ->setAgence($agence)
                    ->setStationneAgence($agence)
                    ->setAgeMinimum($age)
                    ->setCategorie($cat[rand(0,count($cat))-1])
                    ->setNbAnneePermis(rand(0, $age - 18))
                    ->setBoiteVitesse($bt[rand(0,1)])
                    ->setClimatisation((rand(0,100) % 2) == 0 ? "oui" : "non")
                    ->setDirectionAssistee((rand(0,100) % 2) == 0 ? "oui" : "non")
                    ->setMarque($marque)
                    ->setModele($modele[$i])
                    ->setNbPassager(rand(0,4))
                    ->setNbPassager(rand(0,4))
                    ->setEquipement("CD")
                    ->setMoteur($m[rand(0,1)])
                    ->setSupprimee(false)
                ;
                $manager->persist($voiture);
                $manager->flush();
            }
        }
    }
}

