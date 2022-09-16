<?php
/**
 * Created by PhpStorm.
 * User: pfcadenas *
 * Date: 25/05/2017
 * Time: 14:40
 */

namespace Geocuba\PortadoresBundle\Util;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class PieFirmaGtr
 * @package Geocuba\PortadoresBundle\Util
 * @author Pedro Frank Cadenas del Llano
 */
class PieFirmaGtr
{
    private $em;

    function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    public function getPieFirma($documento, $unidad)
    {
        $piefirma = $this->em->getRepository('PortadoresBundle:PieFirma')->findOneBy(array(
            'documento' => $documento,
            'nunidadid' => $unidad
        ));

        if (!is_null($piefirma)) {

            $_html = "<table cellspacing='0' cellpadding='5' border='0' width='100%' style='margin-top: 10px'>";

            $personaConfecciona = is_null($piefirma->getConfecciona()) ? null : $this->em->getRepository('PortadoresBundle:Persona')->find($piefirma->getConfecciona());
            $nombreConfecciona = is_null($personaConfecciona) ? '' : $personaConfecciona->getNombre();
            $cargoConfecciona = is_null($personaConfecciona) ? '' : $personaConfecciona->getCargoid()->getNombre();

            $personaRevisa = is_null($piefirma->getRevisa()) ? null : $this->em->getRepository('PortadoresBundle:Persona')->find($piefirma->getRevisa());
            $nombreRevisa = is_null($personaRevisa) ? '' : $personaRevisa->getNombre();
            $cargoRevisa = is_null($personaRevisa) ? '' : $personaRevisa->getCargoid()->getNombre();

            $personaAutoriza = is_null($piefirma->getAutoriza()) ? null : $this->em->getRepository('PortadoresBundle:Persona')->find($piefirma->getAutoriza());
            $nombreAutoriza = is_null($personaAutoriza) ? '' : $personaAutoriza->getNombre();
            $cargoAutoriza = is_null($personaAutoriza) ? '' : $personaAutoriza->getCargoid()->getNombre();


            $_html .= "<tr>";
            if (!is_null($personaConfecciona))
                $_html .= "<td style='text-align: center; border: none;'><strong>Confeccionado por:</strong></td>";
            if (!is_null($personaRevisa))
                $_html .= "<td style='text-align: center; border: none;'><strong>Revisado por:</strong></td>";
            if (!is_null($personaAutoriza))
                $_html .= "<td style='text-align: center; border: none;'><strong>Aprobado por:</strong></td>";
            $_html .= "</tr>";

            $_html .= "<tr>";
            if (!is_null($personaConfecciona))
                $_html .= "<td style='text-align: center; border: none;'>__________________________</td>";
            if (!is_null($personaRevisa))
                $_html .= "<td style='text-align: center; border: none;'>__________________________</td>";
            if (!is_null($personaAutoriza))
                $_html .= "<td style='text-align: center; border: none;'>__________________________</td>";
            $_html .= "</tr>";

            $_html .= "<tr>";
            if (!is_null($personaConfecciona))
                $_html .= "<td style='text-align: center; border: none;'>$nombreConfecciona</td>";
            if (!is_null($personaRevisa))
                $_html .= "<td style='text-align: center; border: none;'>$nombreRevisa</td>";
            if (!is_null($personaAutoriza))
                $_html .= "<td style='text-align: center; border: none;'>$nombreAutoriza</td>";
            $_html .= "</tr>";

            $_html .= "<tr>";
            if (!is_null($personaConfecciona))
                $_html .= "<td style='text-align: center; border: none;'>$cargoConfecciona</td>";
            if (!is_null($personaRevisa))
                $_html .= "<td style='text-align: center; border: none;'>$cargoRevisa</td>";
            if (!is_null($personaAutoriza))
                $_html .= "<td style='text-align: center; border: none;'>$cargoAutoriza</td>";
            $_html .= "</tr>";

            $_html .= "</table>";

            return $_html;

        } else {
            return '';
        }
    }

    public function getPieFirmaDistribucionPhpWord($documento, $unidad, $word , $section)
    {
        // Define styles
        $multipleTabsStyleName = 'multipleTab';
        $word->addParagraphStyle(
            $multipleTabsStyleName,
            array(
                'tabs' => array(
                    new \PhpOffice\PhpWord\Style\Tab('center', 1500),
                    new \PhpOffice\PhpWord\Style\Tab('center', 5500),
                    new \PhpOffice\PhpWord\Style\Tab('center', 9000),
                    new \PhpOffice\PhpWord\Style\Tab('center', 12500),
                ),
            )
        );

        $rightTabStyleName = 'rightTab';
        $word->addParagraphStyle($rightTabStyleName, array('tabs' => array(new \PhpOffice\PhpWord\Style\Tab('right', 9090))));

        $leftTabStyleName = 'centerTab';
        $word->addParagraphStyle($leftTabStyleName, array('tabs' => array(new \PhpOffice\PhpWord\Style\Tab('center', 4680))));

        $piefirma = $this->em->getRepository('PortadoresBundle:PieFirma')->findOneBy(array(
            'documento' => $documento,
            'nunidadid' => $unidad
        ));

        if (!is_null($piefirma)) {

            $personaConfecciona = is_null($piefirma->getConfecciona()) ? null : $this->em->getRepository('PortadoresBundle:Persona')->find($piefirma->getConfecciona());
            $nombreConfecciona = is_null($personaConfecciona) ? '' : $personaConfecciona->getNombre();
            $cargoConfecciona = is_null($personaConfecciona) ? '' : $personaConfecciona->getCargoid()->getNombre();

            $personaCajera = is_null($piefirma->getCajera()) ? null : $this->em->getRepository('PortadoresBundle:Persona')->find($piefirma->getCajera());
            $nombreCajera = is_null($personaCajera) ? '' : $personaCajera->getNombre();
            $cargoCajera = is_null($personaCajera) ? '' : $personaCajera->getCargoid()->getNombre();

            $personaRevisa = is_null($piefirma->getRevisa()) ? null : $this->em->getRepository('PortadoresBundle:Persona')->find($piefirma->getRevisa());
            $nombreRevisa = is_null($personaRevisa) ? '' : $personaRevisa->getNombre();
            $cargoRevisa = is_null($personaRevisa) ? '' : $personaRevisa->getCargoid()->getNombre();

            $personaAutoriza = is_null($piefirma->getAutoriza()) ? null : $this->em->getRepository('PortadoresBundle:Persona')->find($piefirma->getAutoriza());
            $nombreAutoriza = is_null($personaAutoriza) ? '' : $personaAutoriza->getNombre();
            $cargoAutoriza = is_null($personaAutoriza) ? '' : $personaAutoriza->getCargoid()->getNombre();

            $who = "";
            $who .= !is_null($personaConfecciona)?"\tElaborado por:":"\t ";
            $who .= !is_null($personaCajera)?"\tEntrega y recepción de las tarjetas:":"\t ";
            $who .= !is_null($personaRevisa)?"\tRevisado por:":"\t ";
            $who .= !is_null($personaAutoriza)?"\tAutorizado por:":"\t ";

            $line = "";
            $line .= !is_null($personaConfecciona)?"\t____________________":"\t ";
            $line .= !is_null($personaCajera)?"\t____________________":"\t ";
            $line .= !is_null($personaRevisa)?"\t____________________":"\t ";
            $line .= !is_null($personaAutoriza)?"\t____________________":"\t ";

            $name = "";
            $name .= !is_null($personaConfecciona)?"\t".$nombreConfecciona:"\t ";
            $name .= !is_null($personaCajera)?"\t".$nombreCajera:"\t ";
            $name .= !is_null($personaRevisa)?"\t".$nombreRevisa:"\t ";
            $name .= !is_null($personaAutoriza)?"\t".$nombreAutoriza:"\t ";

            $cargo = "";
            $cargo .= !is_null($personaConfecciona)?"\t".$cargoConfecciona:"\t ";
            $cargo .= !is_null($personaCajera)?"\t".$cargoCajera:"\t ";
            $cargo .= !is_null($personaRevisa)?"\t".$cargoRevisa:"\t ";
            $cargo .= !is_null($personaAutoriza)?"\t".$cargoAutoriza:"\t ";

            $section->addText($who, null, $multipleTabsStyleName);
            $section->addText($line, null, $multipleTabsStyleName);
            $section->addText($name, null, $multipleTabsStyleName);
            $section->addText($cargo, null, $multipleTabsStyleName);
        }
    }


    public function getPieFirmaDistribucion($documento, $unidad)
    {
        $piefirma = $this->em->getRepository('PortadoresBundle:PieFirma')->findOneBy(array(
            'documento' => $documento,
            'nunidadid' => $unidad
        ));



        if (!is_null($piefirma)) {

            $_html = "<table cellspacing='0' cellpadding='5' border='0' width='100%'>";

            $personaConfecciona = is_null($piefirma->getConfecciona()) ? null : $piefirma->getConfecciona();
            $nombreConfecciona = is_null($personaConfecciona) ? '' : $personaConfecciona->getNombre();
            $cargoConfecciona = is_null($personaConfecciona) ? '' : $personaConfecciona->getCargoid()->getNombre();

            $personaCajera = is_null($piefirma->getCajera()) ? null : $piefirma->getCajera();
            $nombreCajera = is_null($personaCajera) ? '' : $personaCajera->getNombre();
            $cargoCajera = is_null($personaCajera) ? '' : $personaCajera->getCargoid()->getNombre();

            $personaRevisa = is_null($piefirma->getRevisa()) ? null : $piefirma->getRevisa();
            $nombreRevisa = is_null($personaRevisa) ? '' : $personaRevisa->getNombre();
            $cargoRevisa = is_null($personaRevisa) ? '' : $personaRevisa->getCargoid()->getNombre();

            $personaAutoriza = is_null($piefirma->getAutoriza()) ? null : $piefirma->getAutoriza();
            $nombreAutoriza = is_null($personaAutoriza) ? '' : $personaAutoriza->getNombre();
            $cargoAutoriza = is_null($personaAutoriza) ? '' : $personaAutoriza->getCargoid()->getNombre();


            $_html .= "<tr>";
            if (!is_null($personaConfecciona))
                $_html .= "<td style='text-align: center; border: none;'><strong>Confeccionado por:</strong></td>";
            if (!is_null($personaCajera))
                $_html .= "<td style='text-align: center; border: none;'><strong>Entrega y recepción de las tarjetas:</strong></td>";
            if (!is_null($personaRevisa))
                $_html .= "<td style='text-align: center; border: none;'><strong>Revisado por:</strong></td>";
            if (!is_null($personaAutoriza))
                $_html .= "<td style='text-align: center; border: none;'><strong>Autorizado por:</strong></td>";
            $_html .= "</tr>";

            $_html .= "<tr>";
            if (!is_null($personaConfecciona))
                $_html .= "<td style='text-align: center; border: none;'>__________________________</td>";
            if (!is_null($personaCajera))
                $_html .= "<td style='text-align: center; border: none;'>__________________________</td>";
            if (!is_null($personaRevisa))
                $_html .= "<td style='text-align: center; border: none;'>__________________________</td>";
            if (!is_null($personaAutoriza))
                $_html .= "<td style='text-align: center; border: none;'>__________________________</td>";
            $_html .= "</tr>";

            $_html .= "<tr>";
            if (!is_null($personaConfecciona))
                $_html .= "<td style='text-align: center; border: none;'>$nombreConfecciona</td>";
            if (!is_null($personaCajera))
                $_html .= "<td style='text-align: center; border: none;'>$nombreCajera</td>";
            if (!is_null($personaRevisa))
                $_html .= "<td style='text-align: center; border: none;'>$nombreRevisa</td>";
            if (!is_null($personaAutoriza))
                $_html .= "<td style='text-align: center; border: none;'>$nombreAutoriza</td>";
            $_html .= "</tr>";

            $_html .= "<tr>";
            if (!is_null($personaConfecciona))
                $_html .= "<td style='text-align: center; border: none;'>$cargoConfecciona</td>";
            if (!is_null($personaCajera))
                $_html .= "<td style='text-align: center; border: none;'>$cargoCajera</td>";
            if (!is_null($personaRevisa))
                $_html .= "<td style='text-align: center; border: none;'>$cargoRevisa</td>";
            if (!is_null($personaAutoriza))
                $_html .= "<td style='text-align: center; border: none;'>$cargoAutoriza</td>";
            $_html .= "</tr>";

            $_html .= "</table>";

            return $_html;

        } else {
            return '';
        }
    }
}
