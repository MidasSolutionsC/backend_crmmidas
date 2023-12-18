<?php

namespace App\Http\Controllers;

use App\Services\Implementation\SaleDetailService;
use App\Services\Implementation\SaleDocumentService;
use App\Services\Implementation\SaleHistoryService;
use Illuminate\Http\Request;
use App\Services\Implementation\ReportService;
use App\Validator\SaleDetailValidator;
use App\Validator\SaleDocumentValidator;
use App\Validator\SaleHistoryValidator;
use App\Validator\ReportValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{

    private $request;
    private $reportService;
    private $reportValidator;

    // Venta detalle
    private $saleDetailService;
    private $saleDetailValidator;
    // Venta documentos
    private $saleDocumentService;
    private $saleDocumentValidator;
    // Venta historial
    private $saleHistoryService;
    private $saleHistoryValidator;

    public function __construct(
        Request $request,
        ReportService $reportService,
        ReportValidator $reportValidator,
        SaleDetailService $saleDetailService,
        SaleDetailValidator $saleDetailValidator,
        SaleDocumentService $saleDocumentService,
        SaleDocumentValidator $saleDocumentValidator,
        SaleHistoryService $saleHistoryService,
        SaleHistoryValidator $saleHistoryValidator
    ) {
        $this->request = $request;
        $this->reportService = $reportService;
        $this->reportValidator = $reportValidator;
        $this->saleDetailService = $saleDetailService;
        $this->saleDetailValidator = $saleDetailValidator;
        $this->saleDocumentService = $saleDocumentService;
        $this->saleDocumentValidator = $saleDocumentValidator;
        $this->saleHistoryService = $saleHistoryService;
        $this->saleHistoryValidator = $saleHistoryValidator;
    }

    public function salesByCoordinator()
    {
        try {

            $validator = $this->reportValidator->validate();

            if ($validator->fails()) {
                $response = $this->responseError($validator->errors(), 422);
            } else {
                $result = $this->reportService->salesByCoordinator($this->request->all());
                if ($result != null) {
                    $response = $this->response([$result]);
                } else {
                    $response = $this->responseError(['message' => 'Error al listar los datos de la venta', 'error' => $result]);
                }
            }

            return $response;
        } catch (\Exception $e) {
            return $this->responseError(['message' => 'Error al listar las ventas', 'error' => $e->getMessage()], 500);
        }
    }

    public function salesBySeller()
    {

        try {

            $validator = $this->reportValidator->validate();

            if ($validator->fails()) {
                $response = $this->responseError($validator->errors(), 422);
            } else {
                $result = $this->reportService->salesBySeller($this->request->all());
                if ($result != null) {
                    $response = $this->response([$result]);
                } else {
                    $response = $this->responseError(['message' => 'Error al listar los datos de la venta', 'error' => $result]);
                }
            }

            return $response;
        } catch (\Exception $e) {
            return $this->responseError(['message' => 'Error al listar las ventas', 'error' => $e->getMessage()], 500);
        }
    }

    public function salesByBrand()
    {

        try {

            $validator = $this->reportValidator->validate();

            if ($validator->fails()) {
                $response = $this->responseError($validator->errors(), 422);
            } else {
                $result = $this->reportService->salesByBrand($this->request->all());
                if ($result != null) {
                    $response = $this->response([$result]);
                } else {
                    $response = $this->responseError(['message' => 'Error al listar los datos de la venta', 'error' => $result]);
                }
            }

            return $response;
        } catch (\Exception $e) {
            return $this->responseError(['message' => 'Error al listar las ventas', 'error' => $e->getMessage()], 500);
        }
    }
}
