<?php

namespace App\Http\Controllers;

use Sametsahindogan\ResponseObjectCreator\ErrorResult;
use Sametsahindogan\ResponseObjectCreator\ErrorService\ErrorBuilder;
use Sametsahindogan\ResponseObjectCreator\SuccessResult;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Spatie\Permission\Exceptions\PermissionAlreadyExists;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get(Request $request): JsonResponse
    {
        $sort = $request->get('sort', 'id');
        $sortType = $request->get('order', 'desc');
        $offset = (int)$request->get('offset', 0);
        $limit = (int)$request->get('limit', 10);

        /** @var Builder $queryBuilder */
        $queryBuilder = (new Permission())->newQuery();

        $data = $queryBuilder->orderBy($sort, $sortType)
            ->offset($offset)
            ->limit($limit)
            ->get();

        return response()->json(new SuccessResult($data->toArray()));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        /** @var Validator $validator */
        $validator = validator($request->all(), [
            'name' => 'required',
        ])->setAttributeNames([
            'name' => 'Name',
        ]);

        if ($validator->fails()) {
            return response()->json(
                new ErrorResult(
                    (new ErrorBuilder())
                        ->title('Operation Failed')
                        ->message($validator->errors()->first())
                        ->extra($validator->errors()->all())
                )
            );
        }

        try {

            $permission = Permission::create(['name' => $request->get('name')]);

        } catch (PermissionAlreadyExists $exception) {
            return response()->json(
                new ErrorResult(
                    (new ErrorBuilder())
                        ->title('Operation Failed')
                        ->message($exception->getMessage())
                )
            );
        }

        return response()->json(new SuccessResult($permission->toArray()));
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $permission = $this->checkRow($id);

        /** @var Validator $validator */
        $validator = validator($request->all(), [
            'name' => 'required'
        ])->setAttributeNames([
            'name' => 'Name',
        ]);

        if ($validator->fails()) {
            return response()->json(
                new ErrorResult(
                    (new ErrorBuilder())
                        ->title('Operation Failed')
                        ->message($validator->errors()->first())
                        ->extra($validator->errors()->all())
                )
            );
        }

        $permission->name = $request->get('name');

        $permission->save();

        return response()->json(new SuccessResult());

    }

    /**
     * @param $id
     * @return mixed
     */
    protected function checkRow($id)
    {
        return Permission::find($id);
    }
}
