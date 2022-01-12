<?php

namespace App\Services;

use Doctrine\ORM\EntityManager;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class BaseService
{
    /**
     * @var EntityManager The Entity Manager
     */
    protected $em;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var ContainerInterface
     */
    protected $container;
    protected $header=[];

    /**
     * Getter of the Entity Manager
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * Setter of the Entity Manager
     *
     * @param EntityManager $em the Entity Manager
     */
    public function setEntityManager($em)
    {
        $this->em = $em;
    }

    /**
     * @param $key
     * @param $class
     * @return \Doctrine\ORM\EntityRepository|\Doctrine\Persistence\ObjectRepository
     */
    public function getRepository($class)
    {
        return $this->em->getRepository($class);
    }

    /**
     * @param $service
     */
    public function setContainer($service)
    {
        $this->container = $service;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(){
        return $this->container;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(){
        return $this->logger;
    }

    /**
     * Exporting sales
     * @param $modelHeader
     * @param $title
     * @param $data
     * @param $columns
     * @param $format
     * @param array $customHeader
     * @return StreamedResponse
     */
    public function export($title, $data, $columns, $format, $customHeader = [])
    {

        foreach ($customHeader as $key => $h) {
            if (isset($this->header[$key])) {
                $this->header[$key] = $h;
            }
        }

        if ($format == 'csv') {

            return $this->exportCsv($data, $columns, $title);

        } elseif ($format == 'xls') {

            return $this->exportXls($data, $columns, $title);

        }
    }

    /**
     * Exporting as csv
     * @param $data
     * @param $columns
     * @param $title
     */
    private function exportCsv($data, $columns, $title)
    {

        $final = [];

        foreach ($columns as $c) {
            if (isset($this->header[$c])) {
                $final[0][] = utf8_decode($this->header[$c]);
            }

        }

        $i = 1;

        foreach ($data as $p) {
            foreach ($columns as $c) {
                if (isset($this->header[$c]))
                    $final[$i][] = strip_tags(utf8_decode($p[$c]));
            }
            $i++;
        }


        $response = new StreamedResponse();
        $response->setCallback(function () use ($final) {
            $handle = fopen('php://output', 'w+');
            foreach ($final as $d)
                fputcsv($handle, $d, ';');
            fclose($handle);
        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $title . '.csv"');
        return $response;
    }

    /**
     * Exporting as Xls
     * @param $data
     * @param $columns
     * @param $title
     * @return mixed
     *
     */
    private function exportXls($data, $columns, $title)
    {
        $streamedResponse = new StreamedResponse();
        $streamedResponse->setCallback(function () use ($columns, $data, $title){
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();



            $col = 'A';
            foreach ($columns as $c) {
                if (isset($this->header[$c])) {

                    $sheet->setCellValue($col++ . '1', $this->header[$c]);
                }
            }
            $i = 2;
            foreach ($data as $p) {
                $col = 'A';
                foreach ($columns as $c) {
                    if (isset($this->header[$c]))
                        $sheet->setCellValue($col++ . $i, strip_tags($p[$c]));
                }
                $i++;
            }


            $sheet->setTitle($title);
            $writer =  new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $streamedResponse->setStatusCode(Response::HTTP_OK);
        $streamedResponse->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $streamedResponse->headers->set('Content-Disposition', 'attachment; filename="'.$title.'.xlsx"');

        return $streamedResponse->send();
    }
}
