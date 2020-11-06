<?php
namespace App\Controller\Client;

use App\Entity\Panier;
use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment;


class PanierController extends AbstractController
{

    /**
     * @Route("/client", name="client_panier_index", methods={"GET"})
     * @Route("/client/panierProduits/show", name="client_panier_showProduits", methods={"GET"})
     */
    public function showPanierProduits(Request $request)
    {
        $produits = $this->getDoctrine()->getRepository(Produit::class)->findBy([], ['typeProduit' => 'ASC', 'stock' => 'ASC']);
        return $this->render('client/boutique/panier_produit.html.twig', ['produits' => $produits, 'monPanier' => NULL]);
    }

    /*
     * @Route("/panier", name="panier_index")
     * @Route("/panier/show", name="panier_show")
     */
    public function show(Request $request, Environment $twig, Registry $doctrine){
        $lignes_panier = $doctrine->getRepository(Panier::class)->findBy(['user' => $this->getUser()]);
        $produits = $doctrine->getRepository(Produit::class)->findby([],['libelle' => ' ASC']);


    }
}