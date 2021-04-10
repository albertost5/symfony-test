<?php

namespace App\Controller;

use App\Entity\Sector;
use App\Entity\Empresa;
use App\Form\EmpresaType;
use App\Repository\EmpresaRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/main/empresas")
 */
class EmpresaController extends AbstractController
{
    /**
     * @Route("/", name="empresa_index", methods={"GET"})
     */
    public function index(EmpresaRepository $empresaRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $empresaRepository = $em->getRepository(Empresa::class);
        $sectorRepository = $em->getRepository(Sector::class);
        $sectores = $sectorRepository->findAll();

        $emp = $em->getRepository('App\Entity\Empresa')->findBy(array('nombre' => "asdasd", 'sector' => 6));
        // dd($emp);

        $query = $empresaRepository->findAll();
        
        $empresas = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('empresa/index.html.twig', [
            'empresas' => $empresas,
            'sectores' => $sectores,
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/new", name="empresa_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $empresa = new Empresa();
        $form = $this->createForm(EmpresaType::class, $empresa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($empresa);
            $entityManager->flush();

            return $this->redirectToRoute('empresa_index');
        }

        return $this->render('empresa/new.html.twig', [
            'empresa' => $empresa,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="empresa_show", methods={"GET"})
     */
    public function show(Empresa $empresa): Response
    {
        return $this->render('empresa/show.html.twig', [
            'empresa' => $empresa,
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="empresa_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Empresa $empresa): Response
    {
        $form = $this->createForm(EmpresaType::class, $empresa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('empresa_index');
        }

        return $this->render('empresa/edit.html.twig', [
            'empresa' => $empresa,
            'form' => $form->createView(),
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/{id}", name="empresa_delete", methods={"POST"})
     */
    public function delete(Request $request, Empresa $empresa): Response
    {
        if ($this->isCsrfTokenValid('delete' . $empresa->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($empresa);
            $entityManager->flush();
        }

        return $this->redirectToRoute('empresa_index');
    }

    /**
     * @Route("/", name="buscar", methods={"POST"})
     */
    public function buscar(Request $request): Response
    {
        $nombre = $request->request->get('nombre');
        $sectorId = $request->request->get('sector');

        $em = $this->getDoctrine()->getManager();
        $empresaRepository = $em->getRepository(Empresa::class);

        $emp = $empresaRepository->findByNombre($nombre);
        // $empresa = $em->getRepository('App\Entity\Empresa')->findBy(array('nombre' => $nombre, 'sector' => $sectorId));
        
        if($emp && $emp->getSector()->getId() == $sectorId){
            return $this->render('empresa/show.html.twig', [
                'empresa' => $emp,
                'user' => $this->getUser()
            ]);
        }else{
            return $this->redirectToRoute('empresa_index');
        }

        
        
    }
}
