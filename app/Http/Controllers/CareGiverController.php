<?php

namespace App\Http\Controllers;

use App\Http\Requests\CareGiverStoreRequest;
use App\Http\Resources\CareGiverResource;
use App\Services\CareGiverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CareGiverController extends Controller
{
    public function __construct(private CareGiverService $careGiverService)
    {
    }

    public function store(CareGiverStoreRequest $request): JsonResponse
    {
        $userUid = $request->user()->uid;
        $data = $request->validated();
        $data['user_uid'] = $userUid;

        try {
            $careGiverDto = $this->careGiverService->createCareGiver($data);

            return (new CareGiverResource($careGiverDto))
                ->response()
                ->setStatusCode(201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function linkImage(Request $request): JsonResponse
    {
        $giverUid = $request->input('giverUid');
        $file = $request->file('file');

        if (!$file) {
            return response()->json(['message' => 'File is required'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $this->careGiverService->linkImageToGiver($giverUid, $file);
            return response()->json(['message' => $result], Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}