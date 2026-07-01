<?php

namespace App\Http\Controllers;

use App\Http\Requests\CareGiverStoreRequest;
use App\Http\Resources\CareGiverResource;
use App\Services\CareNeedOrSkillsService;
use App\Services\CareGiverService;
use App\Services\CertificationService;
use App\Services\ScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CareGiverController extends Controller
{
    public function __construct(
        private CareGiverService $careGiverService,
        private CareNeedOrSkillsService $careNeedOrSkillsService,
        private CertificationService $certificationService,
        private ScheduleService $scheduleService
    ) {
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

    public function getByUid(string $uid): JsonResponse
    {
        $careGiverDto = $this->careGiverService->getByUid($uid);

        if (!$careGiverDto) {
            return response()->json(['message' => 'CareGiver not found'], Response::HTTP_NOT_FOUND);
        }

        return (new CareGiverResource($careGiverDto))
            ->response()
            ->setStatusCode(200);
    }

    public function getAll(): JsonResponse
    {
        $careGivers = $this->careGiverService->getAll();

        return CareGiverResource::collection($careGivers)
            ->response()
            ->setStatusCode(200);
    }

    public function updateCareNeedOrSkills(Request $request): JsonResponse
    {
        $uid = $request->input('uid');
        $careNeedOrSkills = $request->input('careNeedOrSkills', []);

        try {
            $result = $this->careNeedOrSkillsService->updateCareNeedOrSkills($uid, new \App\Dto\CareNeedOrSkillsDto($careNeedOrSkills));
            return response()->json(['careNeedOrSkills' => $result->careNeedOrSkills], Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function updateCertifications(Request $request): JsonResponse
    {
        $giverUid = $request->input('giverUid');
        $certifications = $request->input('certifications', []);

        try {
            $result = $this->certificationService->createOrUpdateGiverCert($giverUid, $certifications);
            return response()->json(['certifications' => $result], Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function updateSchedule(Request $request): JsonResponse
    {
        $careGiverUid = $request->input('careGiverUid');
        $days = $request->input('days', []);
        $startTime = $request->input('startTime');
        $endTime = $request->input('endTime');

        try {
            $scheduleDto = new \App\Dto\ScheduleDto($days, $startTime, $endTime);
            $result = $this->scheduleService->createOrUpdateGiverSchedule($careGiverUid, $scheduleDto);
            return response()->json(['schedule' => $result->toArray()], Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}