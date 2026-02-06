<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\ApteksConnectionsCountRepository;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApteksCounterController extends Controller
{
    public function __construct(
        private readonly ApteksConnectionsCountRepository $repo
    ) {}

    /**
     * Initial state для віджета лічильника аптек
     */
    public function show(): JsonResponse
    {
        $row = $this->repo->latest();

        if (!$row) {
            return response()->json([], Response::HTTP_NO_CONTENT);
        }

        return response()->json($row);
    }
}
