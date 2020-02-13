<?php

namespace App\Http\Controllers;

use Sametsahindogan\ResponseObjectCreator\ErrorResult;
use Sametsahindogan\ResponseObjectCreator\ErrorService\ErrorBuilder;
use Sametsahindogan\ResponseObjectCreator\SuccessResult;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
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
        $queryBuilder = (new Role())->newQuery();

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

            $role = Role::create(['name' => $request->get('name')]);

        } catch (RoleAlreadyExists $exception) {

            return response()->json(
                new ErrorResult(
                    (new ErrorBuilder())
                        ->title('Operation Failed')
                        ->message($exception->getMessage())
                )
            );

        }

        return response()->json(new SuccessResult($role->toArray()));
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $role = $this->checkRow($id);

        /** @var Validator $validator */
        $validator = validator($request->all(), [
            'permissions' => 'required'
        ])->setAttributeNames([
            'permissions' => 'Permissions',
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

        $role->syncPermissions($request->get('permissions'));

        return response()->json(new SuccessResult());

    }

    /**
     * @param $id
     * @return mixed
     */
    protected function checkRow($id)
    {
        return Role::with(['permissions'])->find($id);
    }
}
