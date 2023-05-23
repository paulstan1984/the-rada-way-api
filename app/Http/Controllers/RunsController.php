<?php

namespace App\Http\Controllers;

use App\Models\PhisicalResource;
use Illuminate\Http\Request;
use App\Services\RunsRepository;
use App\Services\PaginationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;

class RunsController extends Controller
{
    var $repository;
    var $paginationService;

    public function __construct(RunsRepository $repository, PaginationService $paginationService)
    {
        $this->repository = $repository;
        $this->paginationService = $paginationService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->search($request, 1);
    }

    public function search(Request $request, $page = 1, $keyword = null, $service_provider_id = 0)
    {
        $query = $this->repository->search($keyword);

        if ($request->user->role == User::SERVICE_PROVIDER) {
            $service_provider_id = $request->user->service_provider_id;
        }

        if ($service_provider_id != 0) {
            $query = $query->where('service_provider_id', $service_provider_id);
        }

        $query = $query->orderBy('name', 'asc');

        $pagination = $this->paginationService->applyPagination($query, $page);

        return $pagination;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user->role == User::SERVICE_PROVIDER) {
            $request->merge(['service_provider_id' => $request->user->service_provider_id]);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:100', Rule::unique('phisical_resources')->where(fn ($query) => $query->where('service_provider_id', $request->service_provider_id))],
            'description' => ['required', 'max:1000'],
            'weekly_timetable' => ['required'],
            'schedule_type' => ['required', Rule::in(['minute', 'hour'])],
            'schedule_units' => ['required'],
            'open' => ['required', Rule::in(['1', '0'])],
            'service_provider_id' => ['required', Rule::exists('service_providers', 'id')]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 404);
        }

        $item = $this->repository->create($validator->validated());

        return response()->json($item, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PhisicalResource  $phisicalResource
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $Id)
    {
        $item = PhisicalResource::find($Id);

        if ($item == null) {
            return response()->json(['error' => 'not found'], 400);
        }

        if ($request->user->role == User::SERVICE_PROVIDER) {
            if ($item->service_provider_id != $request->user->service_provider_id) {
                return response()->json(['error' => 'not found'], 400);
            }
        }

        return response()->json($item, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PhisicalResource  $phisicalResource
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $Id)
    {
        $item = PhisicalResource::find($Id);

        if ($item == null) {
            return response()->json(['error' => 'not found'], 400);
        }

        if ($request->user->role == User::SERVICE_PROVIDER) {
            if ($item->service_provider_id != $request->user->service_provider_id) {
                return response()->json(['error' => 'not found'], 400);
            }

            $request->merge(['service_provider_id' => $request->user->service_provider_id]);
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 'max:100',
                Rule::unique('phisical_resources')
                    ->ignore($item->id)
                    ->where(fn ($query) => $query->where('service_provider_id', $request->service_provider_id))
            ],
            'description' => ['required', 'max:1000'],
            'weekly_timetable' => ['required'],
            'schedule_type' => ['required', Rule::in(['minute', 'hour'])],
            'schedule_units' => ['required'],
            'open' => ['required', Rule::in(['1', '0'])],
            'service_provider_id' => ['required', Rule::exists('service_providers', 'id')]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 404);
        }

        $item = $this->repository->update($item, $validator->validated());

        return response()->json(PhisicalResource::find($Id), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PhisicalResource  $phisicalResource
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $Id)
    {
        $item = PhisicalResource::find($Id);

        if ($item == null) {
            return response()->json(['error' => 'not found'], 400);
        }

        if ($request->user->role == User::SERVICE_PROVIDER) {
            if ($item->service_provider_id != $request->user->service_provider_id) {
                return response()->json(['error' => 'not found'], 400);
            }
        }

        $this->repository->delete($item);

        return response()->json(true, 200);
    }
}
