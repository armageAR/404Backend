<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * @Route("/mc")
 */
class MissingChildrenController extends AbstractController
{
    const APIURL = "http://404be-api.test/mc/";

    /**
     * @Route("/activas")
     */
    public function index()
    {
        $content = file_get_contents(self::APIURL);
        return $this->render('missing_children/index.html.twig', [
            'kids' => json_decode($content, true),
        ]);
    }

    /**
     * @Route(path="/json")
     */
    public function generarJson(Request $request)
    {
        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->add('ChicosId', TextType::class)
            ->add('Enviar', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();
            $content = file_get_contents(self::APIURL . 'missing/' . $data["ChicosId"]);

            // print_r($content);
            // exit;

            return $this->render('missing_children/json.html.twig', [
                'json' => json_decode($content, true),
            ]);
        }

        return $this->render('missing_children/jsonGenerate.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
