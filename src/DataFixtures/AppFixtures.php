<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\TypeProduit;
use App\Entity\Produit;
use phpDocumentor\Reflection\Types\Integer;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $this->loadTypeProduits($manager);
        $this->loadProduits($manager);
        $manager->flush();
    }

    private function loadTypeProduits(ObjectManager $manager)
    {
        $typesProduits = [
            ['id' => 1,'libelle' => 'Fourniture de bureau'],
            ['id' => 2,'libelle' => 'Mobilier'],
            ['id' => 3,'libelle' => 'Mobilier Jardin'],
            ['id' => 4,'libelle' => 'Arrosage'],
            ['id' => 5,'libelle' => 'Outils'],
            ['id' => 6,'libelle' => 'Divers']
        ];
        foreach ($typesProduits as $type)
        {
            $type_new = new TypeProduit();
            $type_new->setLibelle($type['libelle']);
            echo $type_new."\n";
            $manager->persist($type_new);
            $manager->flush();
        }
    }

    private function loadProduits(ObjectManager $manager)
    {
        $produits = [
            [ 'nom' => 'Enveloppes (50p)', 'prix' => '2', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Fourniture de bureau', 'photo' => null],
            [ 'nom' => 'Stylo noir', 'prix' => '1', 'stock' => '13', 'disponible' => false, 'typeProduit'  => 'Fourniture de bureau', 'photo' => 'stylo.jpeg'],
            [ 'nom' => 'Boite de rangement', 'prix' => '3', 'stock' => '12', 'disponible' => true, 'typeProduit'  => 'Fourniture de bureau', 'photo' => 'boites.jpeg'],
            [ 'nom' => 'Chaise', 'prix' => '40', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Mobilier', 'photo' => 'chaise.jpeg'],
            [ 'nom' => 'Tables', 'prix' => '200', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Mobilier', 'photo' => 'table.jpeg'],
            [ 'nom' => 'Salon de Jardin alu', 'prix' => '149', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Mobilier Jardin', 'photo' => 'salonJardin2.jpg'],
            [ 'nom' => 'Table+6 fauteuilles de Jardin', 'prix' => '790', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Mobilier Jardin', 'photo' => 'tableFauteuilsJardin1.jpg'],
            [ 'nom' => 'Set Table + 4 bancs', 'prix' => '229', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Mobilier Jardin', 'photo' => 'setTableChaises.jpg'],
            [ 'nom' => 'arrosoir bleu', 'prix' => '13.50', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Arrosage', 'photo' => 'arrosoir1.jpg'],
            [ 'nom' => 'arrosoir griotte', 'prix' => '9.90', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Arrosage', 'photo' => 'arrosoir2.jpg'],
            [ 'nom' => 'tuyau arrosage', 'prix' => '31.90', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Arrosage', 'photo' => 'tuyauArrosage1.jpg'],
            [ 'nom' => 'tournevis', 'prix' => '23.90', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Outils', 'photo' => 'lotTourneVis.jpg'],
            [ 'nom' => 'marteau menuisier', 'prix' => '7.80', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Outils', 'photo' => 'marteau.jpg'],
            [ 'nom' => 'pince multiprise', 'prix' => '21.80', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Outils', 'photo' => 'pinceMultiprise.jpg'],
            [ 'nom' => 'perceuse', 'prix' => '149.80', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Outils', 'photo' => 'perceuse.jpg'],
        ];
        foreach ($produits as $produit)
        {
            $new_produit = new Produit();
            $new_produit->setNom($produit['nom']);
            $new_produit->setPrix($produit['prix']);
            $new_produit->setPhoto($produit['photo']);
            $new_produit->setStock($produit['stock']);
            if($produit['stock'] <10)
                $new_produit->setDateLancement(\DateTime::createFromFormat('Y-m-d','2020-10-19'));
            else
                $new_produit->setDateLancement(Null);
            $new_produit->setDisponible($produit['disponible']);
            $type_produit = $manager->getRepository(TypeProduit::class)->findOneBy(["libelle"  =>  $produit['typeProduit']] );
            if($type_produit != null)
                $new_produit->setTypeProduit($type_produit);
            echo $new_produit."\n";
            $manager->persist($new_produit);
            $manager->flush();
        }
    }
}
