<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateSpecificationFormRequest;
use App\Http\Resources\SpecificationResource;
use App\Models\Specification;
use Illuminate\Http\Request;

class SpecificationController extends Controller
{
    private $repository;

    public function __construct(Specification $specification)
    {
        $this->repository = $specification;
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $spefications = $this->repository->paginate(10);

        return SpecificationResource::collection($spefications);
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function all()
    {
        $spefications = $this->repository->get();

        return SpecificationResource::collection($spefications);
    }

    /**
     * @param StoreUpdateSpecificationFormRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateSpecificationFormRequest $request)
    {
        $specification = $this->repository->create($request->all());

        return response(new SpecificationResource($specification));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!$specification = $this->repository->find($id)) {
            return response(['message' => 'Specification not Found!'], 404);
        }

        return response(new SpecificationResource($specification));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!$specification = $this->repository->find($id)) {
            return response(['message' => 'Specification not Found!'], 404);
        }

        $specification->update($request->all());

        return response(new SpecificationResource($specification));

    }

    /**
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!$specification = $this->repository->find($id)) {
            return response(['message' => 'Specification not Found!'], 404);
        }

        $specification->delete();

        return response([], 204);
    }

}
