<?php

namespace Esimed\CarLocation\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Devis controller.
 *
 * @Route("/pdf")
 */
class PdfController extends Controller {

    /**
     * Génére un pdf pour l'impréssion d'un devis
     *
     * @Route("/devis/{id}", name="devis_pdf")
     */
    public function devisAction($id) {

        $em = $this->getDoctrine()->getEntityManager();
        $devis = $em->getRepository('EsimedCarLocationBackendBundle:devis')->find($id);

        if (!$devis) {
            throw $this->createNotFoundException('Impossible de trouver le devis.');
        }

        $html = $this->renderView('EsimedCarLocationBackendBundle:Pdf:devis.print.html.twig',
            array('devis' => $devis));

        $html2pdf = new \Html2pdf_Html2pdf('P','A4','fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->pdf->IncludeJS("print(true);");
        $html2pdf->writeHTML($html);
        $fichier = $html2pdf->Output('devis-' . $devis->getId() . '.pdf');

        return new Response(
            $fichier,
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="devis-' . $devis->getId() . '.pdf"'   //replace inline by attachment for download
            )
        );

    }

    /**
     * Génére un pdf pour l'impréssion de la facture
     *
     * @Route("/facture/{id}", name="facture_pdf")
     */
    public function factureAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $facture = $em->getRepository('EsimedCarLocationBackendBundle:facture')->find($id);

        if (!$facture) {
            throw $this->createNotFoundException('Impossible de trouver la facture.');
        }

        $html = $this->renderView('EsimedCarLocationBackendBundle:Pdf:facture.print.html.twig',
            array('facture' => $facture));

        $html2pdf = new \Html2pdf_Html2pdf('P','A4','fr');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->pdf->IncludeJS("print(true);");
        $html2pdf->writeHTML($html);
        $fichier = $html2pdf->Output('devis-' . $facture->getId() . '.pdf');

        return new Response(
            $fichier,
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="facture-' . $facture->getId() . '.pdf"'   //replace inline by attachment for download
            )
        );
    }
}