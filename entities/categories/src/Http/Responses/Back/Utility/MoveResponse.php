<?php

namespace InetStudio\CategoriesPackage\Categories\Http\Responses\Back\Utility;

use Illuminate\Http\Request;
use InetStudio\CategoriesPackage\Categories\Contracts\Http\Responses\Back\Utility\MoveResponseContract;

/**
 * Class MoveResponse.
 */
class MoveResponse implements MoveResponseContract
{
    /**
     * @var int
     */
    protected $result;

    /**
     * MoveResponse constructor.
     *
     * @param  int  $result
     */
    public function __construct(int $result)
    {
        $this->result = $result;
    }

    /**
     * Возвращаем результат перемещения объекта.
     *
     * @param  Request  $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function toResponse($request)
    {
        return response()->json(
            [
                'success' => true,
                'result' => $this->result,
            ]
        );
    }
}
