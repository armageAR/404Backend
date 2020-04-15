<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


class ApiMissingChildrenController extends AbstractController
{
    const MCSITE = 'http://www.missingchildren.org.ar/';
    const MCLOOSE = 'listado.php?categoria=perdidos';
    const MCKID = 'datos.php?action=view&id=';

    /**
     * @Route("/apimc")
     */
    public function index()
    {

        $url = self::MCSITE . self::MCLOOSE;



        $dom = new \DOMDocument();
        @$dom->loadHTMLFile($url);

        $tables = $dom->getElementsByTagName('table');
        $person = array();

        foreach ($tables as $table) {

            //Discard the first Table
            if ($table->nodeValue == 'First') {
                continue;
            }

            $rows = $table->getElementsByTagName('tr');

            //id
            $links = $rows[0]->getElementsByTagName('a');
            $link = $links[0]->getAttribute('href');
            parse_str(parse_url($link, PHP_URL_QUERY), $query);
            $id = $query['id'];

            //img url
            $imgs = $rows[0]->getElementsByTagName('img');
            $image = $imgs[0]->getAttribute('src');

            //Name
            $cols = $rows[1]->getElementsByTagName('td');
            $name = $cols[0]->nodeValue;

            //Encontrada
            $found = FALSE;
            if (strpos($name, 'ENCONTRAD')) {
                $found = TRUE;
                $name = str_replace(" FUE ENCONTRADA", "", $name);
                $name = str_replace(" FUE ENCONTRADO", "", $name);
            }
            $name = trim($name);

            if (!$found) {

                $data = $this->kid($id);


                $person[$id] = array(
                    "fullName" => $name,
                    "missingSince" => $data[1],
                    "birthdate" => $data[4],
                    "residenceCity" => $data[5],
                    "residenceState" => $data[6],
                    "imgUrl" => $image,
                    "imgAge" => $data[2],
                    "searchActive" => !$found
                );
            }
        }

        return $this->json($person);
    }

    /**
     * @Route("/apimc/kidtest/{id}")
     */
    public function kidtest($id)
    {
        print_r($this->kid($id));
        exit;
        return null;
    }


    /**
     * @Route("/apimc/kid/{id}")
     */
    public function kid($id)
    {
        $domKid = new \DOMDocument();
        $kidUrl = self::MCSITE . self::MCKID . $id;
        @$domKid->loadHTMLFile($kidUrl);
        $classname = "titulo";
        $finder = new \DomXPath($domKid);
        $spaner = $finder->query("//*[contains(@class, '$classname')]");
        $text = $spaner->item(0)->nodeValue;

        $buscar = array('FALTA DESDE:', 'Edad en la foto:', 'Edad actual:', 'FECHA DE NACIMIENTO:', 'LUGAR DE RESIDENCIA:');
        $text = str_replace($buscar, "++", $text);
        $textData = explode('++', $text);
        //$textData = array_map('trim', $textData);
        if (sizeof($textData) > 3) {
            if (strlen(trim($textData[1])) > 5) {
                $textData[1] = $this->convertFecha($textData[1]);
            }
            //echo strlen(trim($textData[4]));
            if (strlen(trim($textData[4])) > 5) {
                $textData[4] = $this->convertFecha($textData[4]);
            }
            $textData[0] = trim(preg_replace("/[^,0-9a-zA-Z ]/", "", $textData[0]));

            $textData[2] = preg_replace("/[^0-9]/", "", $textData[2]);
            $textData[3] = preg_replace("/[^0-9]/", "", $textData[3]);

            $textData[5] = preg_replace("/[^,0-9a-zA-Z ]/", "", $textData[5]);
            $res = explode(',', $textData[5]);
            $textData[5] = $res[0] ?? '';
            $textData[6] = $res[1] ?? '';
            $textData[5] = trim($textData[5]);
            $textData[6] = trim($textData[6]);

            return ($textData);
        } else {
            return null;
        }
    }

    private function get_string_between($content, $start, $end)
    {
        $r = explode($start, $content);
        if (isset($r[1])) {
            $r = explode($end, $r[1]);
            return $r[0];
        }
        return '';
    }

    private function convertFecha($fecha)
    {

        $fecha = explode(' de ', $fecha);

        $result = preg_replace("/[^0-9]/", "", $fecha[2]) . '-' . $this->cambiarMes($fecha[1]) . '-' . preg_replace("/[^0-9]/", "", $fecha[0]);

        return date("Y-m-d", strtotime($result));
    }

    private function cambiarMes($mes)
    {
        $mes = strtolower($mes);
        $todosMeses = array('enero' => '1', 'febrero' => '2', 'marzo' => '3', 'abril' => '4', 'mayo' => '5', 'junio' => '6', 'julio' => '7', 'agosto' => '8', 'septiembre' => '9', 'octubre' => '10', 'noviembre' => '11', 'diciembre' => '12');
        return $todosMeses[$mes];
    }

    /**
     * @Route("/apimc/json/{kids}")
     */
    public function siteJson($kids)
    {
        $ids = explode(",", $kids);
        $result = array();

        foreach ($ids as $id) {
            $data = $this->kid($id);
            $result[$id] = array(
                "id" => $id,
                "active" => true,
                "encontrado" => false,
                "nombre" => $data[0],
                "apellido" => "",
                "fecha_desaparicion" => $data[1],
                "fecha_foto" => "",
                "edad_foto" => $data[2],
                "fecha_nacimiento" => $data[4],
                "residencia" => $data[5] . ', ' . $data[6]

            );
        }

        return $this->json($result);
    }

    /**
     * @Route("/apimc/imgs/{kids}")
     */
    public function siteImg($kids)
    {
        $ids = explode(",", $kids);
        $result = '';

        foreach ($ids as $id) {
            $result .= $id . '<img src="' . self::MCSITE . $this->kidImage($id) . '" width="300" height="300"><br>';
        }

        return $this->render('imagenes.html.twig', ['result' => $result]);

        // return new Response(
        //     '<html><body>
        //     ' . $result . '
        //     </body></html>'
        // );
    }

    private function kidImage($id)
    {
        $domKid = new \DOMDocument();
        $kidUrl = self::MCSITE . self::MCKID . $id;
        @$domKid->loadHTMLFile($kidUrl);
        $imgs = $domKid->getElementsByTagName('img');
        return $imgs[2]->getAttribute('src');
    }
}
