<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;    // objet REQUEST
use Symfony\Component\HttpFoundation\Response;    // objet RESPONSE

use App\Entity\Produit;
use App\Entity\TypeProduit;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

/**
 * @Route(name="admin_", path="/admin")
 */

class ProduitV2Controller extends AbstractController
{
    /**
     * @Route("/produitv2", name="produitv2_index", methods={"GET"})
     * @Route("/", name="produitv2_index2", methods={"GET"})
     */
    public function index()
    {
        return $this->redirectToRoute('admin_produitv2_show');
    }
    /**
     * @Route("/produitv2/show", name="produitv2_show", methods={"GET"})
     */
    public function showProduits(Request $request)
    {
        $produits = $this->getDoctrine()->getRepository(Produit::class)->findBy([],['typeProduit' => 'ASC','stock' =>'ASC']);

        return $this->render('admin/produitv2/showProduits.html.twig', ['produits' => $produits]);
    }
    /**
     * @Route("/produitv2/add", name="produitv2_add", methods={"GET","POST"})
     */
    public function addProduit(Request $request)
    {
        // A modifier : Utiliser la méthode findBy du Repository : TypeProduitRepository (trier les types de produits par libelle)
        $typeProduits=$this->getDoctrine()->getRepository(TypeProduit::class)->findBy([],['libelle'=>'ASC']);
        // fin A modifier
        if($request->getMethod() == 'GET'){
            return $this->render('admin/produitv2/addProduit.html.twig', ['typeProduits'=> $typeProduits]);
        }

        if(!$this->isCsrfTokenValid('form_produit', $request->get('token'))) {
            throw new  InvalidCsrfTokenException('Invalid CSRF token formulaire produit');
        }                         // ne pas oublier :  use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
        $donnees['nom']=$_POST['nom'];
        $donnees['prix']=$_POST['prix'];
        $donnees['dateLancement']=$request->request->get('dateLancement');
        $donnees['stock']=$request->request->get('stock');
        $donnees['disponible']=$request->request->get('disponible');
        $donnees['photo']=$request->request->get('photo');


        $donnees['typeProduit_id']=$request->request->get('typeProduit_id');  //htmlentities htmlspecialchar

        $erreurs=$this->validatorProduit($donnees);
        dump($erreurs);
        if( empty($erreurs))
        {
            // A modifier
            // créer une entité Produit (instance de) et utiliser les setters de cette entité pour modifier les valeurs puis persister cette entité
            $produit = new Produit();
            $produit->setNom($donnees['nom']);
            $typeProduit=$this->getDoctrine()->getRepository(TypeProduit::class)->find($donnees['typeProduit_id']);
            $produit->setTypeProduit($typeProduit);
            $produit->setDateLancement(\DateTime::createFromFormat('d/m/Y',$donnees['dateLancement']));
            $produit->setPrix($donnees['prix']);
            if($donnees['disponible'] == 'false')  $produit->setDisponible(false);
            else $produit->setDisponible(true);
            $produit->setPhoto($donnees['photo']);
            $produit->setStock($donnees['stock']);
            $this->getDoctrine()->getManager()->persist($produit);
            $this->getDoctrine()->getManager()->flush();
            // fin A modifier
            return $this->redirectToRoute('admin_produit_show');
        }

        // A modifier : Utiliser la méthode findBy du Repository : TypeProduitRepository (trier les types de produits par libelle)
        $typeProduits=$this->getDoctrine()->getRepository(TypeProduit::class)->findBy([],['libelle'=>'ASC']);
        // fin A modifier
        return $this->render('admin/produitv2/addProduit.html.twig', ['donnees'=>$donnees,'erreurs'=>$erreurs,'typeProduits'=> $typeProduits]);
    }



    /**
     * @Route("/produitv2/delete", name="produitv2_delete", methods={"DELETE"})
     */
    public function deleteProduit(Request $request)
    {
        // A modifier : Utiliser la méthode findBy du Repository : TypeProduitRepository
        // fin A modifier

        if(!$this->isCsrfTokenValid('produit_delete', $request->get('token'))) {
            throw new  InvalidCsrfTokenException('Invalid CSRF token formulaire produit');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $id= $request->request->get('id');
        $produit = $entityManager->getRepository(Produit::class)->find($id);
        if (!$produit)  throw $this->createNotFoundException('No produit found for id '.$id);

        //$donnees=$entityManager->getRepository(AutreTableJointe::class)->findBy(['produit' => $produit]);
        //$donnees2=$entityManager->getRepository(AutreTableJointe2::class)->findBy(['produit' => $produit]);

        //  if(empty($donnees)){
        $entityManager->remove($produit);
        $entityManager->flush();
        return $this->redirectToRoute('admin_produit_show');
        // }
        //  else return $this->render('admin/produitv2/ErrorDeleteProduit.html.twig',['nombre' => $nombre]);
    }



    /**
     * @Route("/produitv2/details", name="produitv2_details", methods={"GET"})
     */
    public function detailsProduit(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $typeProduits=$entityManager->getRepository("App:Produit")->getDetailsProduits();
        dump($typeProduits);

        return $this->render('admin/produitv2/detailsTypeProduit.html.twig', ['typeProduits' => $typeProduits]);


    }

    /**
     * @Route("/produitv2/edit/{id}", name="produitv2_edit", methods={"GET"})
     * @Route("/produitv2/edit", name="produitv2_edit_valid", methods={"PUT"})
     */
    public function editProduit(Request $request, $id=null)
    {
        $entityManager = $this->getDoctrine()->getManager();
        // A modifier : Utiliser la méthode findBy du Repository : TypeProduitRepository
        // fin A modifier
        $typeProduits=$this->getDoctrine()->getRepository(TypeProduit::class)->findBy([],['libelle'=>'ASC']);
        if($request->getMethod() == 'GET'){
            $produit = $entityManager->getRepository(Produit::class)->find($id);
            if (!$produit)  throw $this->createNotFoundException('No produit found for id '.$id);
            $dateLancement= $produit->getDateLancement();
            return $this->render('admin/produitv2/editProduit.html.twig', ['typeProduits'=> $typeProduits, 'donnees' => $produit, 'dateLancement' => $dateLancement]);
        }

        if(!$this->isCsrfTokenValid('form_produit', $request->get('token'))) {
            throw new  InvalidCsrfTokenException('Invalid CSRF token formulaire produit');
        }                         // ne pas oublier :  use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
        $donnees['nom']=$_POST['nom'];
        $donnees['prix']=$_POST['prix'];
        $donnees['dateLancement']=$request->request->get('dateLancement');
        $donnees['stock']=$request->request->get('stock');
        $donnees['disponible']=$request->request->get('disponible');
        $donnees['photo']=$request->request->get('photo');
        $donnees['id']=$request->request->get('id');

        $donnees['typeProduit_id']=$request->request->get('typeProduit_id');  //htmlentities htmlspecialchar
        $erreurs=$this->validatorProduit($donnees);
        dump($donnees);
        if( empty($erreurs))
        {
            // A modifier
            $produit = $entityManager->getRepository(Produit::class)->find($donnees['id']);
            if (!$produit)  throw $this->createNotFoundException('No produit found for id '.$donnees['id']);
            $produit->setNom($donnees['nom']);
            $typeProduit=$entityManager->getRepository(TypeProduit::class)->find($donnees['typeProduit_id']);
            if (!$typeProduit)  throw $this->createNotFoundException('No produit found for id '.$donnees['id']);
            $produit->setTypeProduit($typeProduit);
            $produit->setDateLancement(\DateTime::createFromFormat('d/m/Y',$donnees['dateLancement']));
            $produit->setPrix($donnees['prix']);
            if($donnees['disponible'] == 'false')  $produit->setDisponible(false);
            else $produit->setDisponible(true);
            if($donnees['photo'] != "")
                $produit->setPhoto($donnees['photo']);
            $produit->setStock($donnees['stock']);
            $entityManager->persist($produit);
            $entityManager->flush();
            return $this->redirectToRoute('admin_produit_show');
        }

        // A modifier : Utiliser la méthode findBy du Repository : TypeProduitRepository
        $typeProduits=$this->getDoctrine()->getRepository(TypeProduit::class)->findBy([],['libelle'=>'ASC']);
        // fin A modifier
        return $this->render('admin/produitv2/editProduit.html.twig', ['donnees'=>$donnees,'erreurs'=>$erreurs,'typeProduits'=> $typeProduits]);
    }





    public function validatorProduit($donnees)
    {
        $erreurs=array();

        if (! preg_match("/^[A-Za-z ]{1,}/",$donnees['nom'])) $erreurs['nom']='nom composé de 2 lettres minimum';

        if(! is_numeric($donnees['prix'])) $erreurs['prix'] = 'saisir une valeur numérique';

        $dateConvert=\DateTime::createFromFormat('d/m/Y',$donnees['dateLancement']);
        if($dateConvert==NULL)
            $erreurs['dateLancement']='la date doit être au format JJ/MM/AAAA';
        else{
            if($dateConvert->format('d/m/Y') !== $donnees['dateLancement'])
                $erreurs['dateLancement']='la date n\'est pas valide (format jj/mm/aaaa)';
        }

        if(! is_numeric($donnees['stock'])) $erreurs['stock'] = 'saisir une valeur';

        if (! preg_match("/[A-Za-z0-9]{2,}.(jpeg|jpg|png)/",$donnees['photo']))
            $erreurs['photo']='nom de fichier incorrect (extension jpeg , jpg ou png)';

//     /*   if($donnees['photo'] != "")
//        {
//            $file = './assets/images/'.$donnees['photo'];
//            if (! file_exists($file)) {
//                $erreurs['photo']='la photo qui n existe pas le dossier assets/images/';
//            }
//        }*/

        if(isset($donnees['id']) and ! is_numeric($donnees['id']) )  $erreurs['id']='type id incorrect';

        if(! is_numeric($donnees['typeProduit_id'])) $erreurs['typeProduit_id'] = 'saisir une valeur';
        if($donnees['disponible'] != "true" AND  $donnees['disponible'] != "false") $erreurs['disponible'] = 'pb disponible';

        return $erreurs;
    }
}
