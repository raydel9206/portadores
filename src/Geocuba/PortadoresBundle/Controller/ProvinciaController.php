<?php
/**
 * Created by PhpStorm.
 * User: asisoftware13
 * Date: 20/05/2018
 * Time: 13:45
 */

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use Geocuba\AdminBundle\Repository\QueryHelper;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\{
    JsonResponse, Request, Response
};
use Geocuba\PortadoresBundle\Entity\Provincia;
use Geocuba\PortadoresBundle\Entity\Municipio;
use Symfony\Component\HttpKernel\Exception\{
    HttpException, NotFoundHttpException, UnprocessableEntityHttpException
};

class ProvinciaController extends Controller
{

    const FLUSH_THRESHOLD = 100;

    use ViewActionTrait;

    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PortadoresBundle:Provincia')->findBy(array('visible' => true), array('codigo' => 'ASC'));
        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'codigo' => $entity->getCodigo(),
            );
        }

        return new JsonResponse(array('rows' => $_data));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nombre = trim($request->get('nombre'));
        $codigo = $request->get('codigo');

        $repetido = $em->getRepository('PortadoresBundle:Provincia')->buscarProvinciaRepetido($nombre,'');
        if ($repetido > 0) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una provincia con el mismo nombre.'));
        }

        $repetido = $em->getRepository('PortadoresBundle:Provincia')->buscarProvinciaRepetido('', $codigo);
        if ($repetido > 0) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una provincia con el mismo código.'));
        }

        $entity = new Provincia();
        $entity->setNombre($nombre);
        $entity->setCodigo($codigo);
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Provincia adicionada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function editAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nombre = trim($request->get('nombre'));
        $codigo = trim($request->get('codigo'));

        $repetido = $em->getRepository('PortadoresBundle:Provincia')->buscarProvinciaRepetido($nombre, '', $id);
        if ($repetido > 0) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una provincia con el mismo nombre.'));
        }

        $repetido = $em->getRepository('PortadoresBundle:Provincia')->buscarProvinciaRepetido('', $codigo, $id);
        if ($repetido > 0) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una provincia con el mismo código.'));
        }

        $entity = $em->getRepository('PortadoresBundle:Provincia')->find($id);
        $entity->setNombre($nombre);
        $entity->setCodigo($codigo);
        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Provincia modificada con éxito.'));
            return $response;
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function deleteAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $provincia = $em->getRepository('PortadoresBundle:Provincia');

        try {
            $em->transactional(function () use ($request, $provincia) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $provincia_id) {
                    /** @var Provincia $um */
                    $um = $provincia->find($provincia_id);

                    if (!$um) {
                        throw new NotFoundHttpException(sprintf('No existe Provincia con identificador <strong>%s</strong>', $provincia_id));
                    }
                    $em->persist(
                        $um->setVisible(false)
                    );
                }
            });
            $em->clear();
        } catch (\Exception $e) {
            $em->clear();

            if ($e instanceof HttpException) {
                return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
            }

            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        } catch (\Throwable $e) {
        }
        return new JsonResponse(['success' => true, 'message' => \count($request->request->get('ids')) === 1 ? 'Provincia eliminada con éxito' : 'Provincias eliminadas con éxito']);
    }

    public function listMunicipioAction(Request $request)
    {
        $provinciaid = $request->get('id');

        if ($provinciaid) {

            $entities = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:Municipio')->findBy(array(
                'visible' => true,
                'provinciaid' => $provinciaid
            ), array('codigo' => 'ASC'));
        } else {
            $entities = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:Municipio')->findBy(array(
                'visible' => true,
            ), array('nombre' => 'asc'));
        }

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'codigo' => $entity->getCodigo(),
                'provinciaid' => $entity->getProvinciaid()->getId()
            );
        }
        return new JsonResponse(array('rows' => $_data));
    }

    public function addMunicipioAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nombre = trim($request->get('nombre'));
        $provinciaid = $request->get('provinciaid');
        $codigo = $request->get('codigo');

        $repetido = $em->getRepository('PortadoresBundle:Municipio')->buscarMunicipioRepetido($provinciaid, $nombre, '');
        if ($repetido > 0) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un municipio con el mismo nombre en la provincia.'));
        }

        $repetido = $em->getRepository('PortadoresBundle:Municipio')->buscarMunicipioRepetido($provinciaid, '', $codigo);
        if ($repetido > 0) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un municipio con el mismo código en la provincia.'));
        }

        $provincia = $em->getRepository('PortadoresBundle:Provincia')->find($provinciaid);

        $entity = new Municipio();
        $entity->setNombre($nombre);
        $entity->setProvinciaid($provincia);
        $entity->setCodigo($codigo);
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Municipio adicionado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function editMunicipioAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nombre = trim($request->get('nombre'));
        $provinciaid = $request->get('provinciaid');
        $codigo = $request->get('codigo');

        $provincia = $em->getRepository('PortadoresBundle:Provincia')->find($provinciaid);

        $repetido = $em->getRepository('PortadoresBundle:Municipio')->buscarMunicipioRepetido($provinciaid, $nombre, '' ,  $id);
        if ($repetido > 0) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un municipio con el mismo nombre en la provincia.'));
        }

        $repetido = $em->getRepository('PortadoresBundle:Municipio')->buscarMunicipioRepetido($provinciaid, '' , $codigo, $id);
        if ($repetido > 0) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un municipio con el mismo código en la provincia.'));
        }


        $entity = $em->getRepository('PortadoresBundle:Municipio')->find($id);
        $entity->setNombre($nombre);
        $entity->setProvinciaid($provincia);
        $entity->setCodigo($codigo);
        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Municipio modificado con éxito.'));
            return $response;
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function deleteMunicipioAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $municipio = $em->getRepository('PortadoresBundle:Municipio');

        try {
            $em->transactional(function () use ($request, $municipio) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $municipio_id) {
                    /** @var Municipio $um */
                    $um = $municipio->find($municipio_id);

                    if (!$um) {
                        throw new NotFoundHttpException(sprintf('No existe un Municipio con identificador <strong>%s</strong>', $municipio_id));
                    }
                    $em->persist(
                        $um->setVisible(false)
                    );
                }
            });
            $em->clear();
        } catch (\Exception $e) {
            $em->clear();

            if ($e instanceof HttpException) {
                return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
            }

            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        } catch (\Throwable $e) {
        }
        return new JsonResponse(['success' => true, 'message' => \count($request->request->get('ids')) === 1 ? 'Municipio eliminado con éxito' : 'Municipios eliminados con éxito']);
    }

}