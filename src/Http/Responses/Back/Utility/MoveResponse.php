<?php

namespace InetStudio\Categories\Http\Responses\Back\Utility;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\Responsable;
use InetStudio\Categories\Contracts\Http\Responses\Back\Utility\MoveResponseContract;

/**
 * Class MoveResponse.
 */
class MoveResponse implements MoveResponseContract, Responsable
{
    /**
     * @var string
     */
    private $result;

    /**
     * SlugResponse constructor.
     *
     * @param int $result
     */
    public function __construct(int $result)
    {
        $this->result = $result;
    }

    /**
     * Возвращаем количество измененных объектов.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return JsonResponse
     */
    public function toResponse($request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'result' => $this->result,
        ]);
    }
}
