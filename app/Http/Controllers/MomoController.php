<?php

namespace App\Http\Controllers;

use App\Services\MomoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class MomoController extends Controller
{
    public function __construct(private MomoService $momoService) {}

    public function testMomoPayment(): JsonResponse
    {
        try {
            $payUrl = $this->momoService->createSandboxMomoLink('13', '1000');

            return response()->json(['payUrl' => $payUrl], Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
