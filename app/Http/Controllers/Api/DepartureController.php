<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Helpers\Role;
use App\Http\Controllers\Controller;
use App\Models\Departure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartureController extends Controller
{
    use Role;
    public function index(): JsonResponse {
        $departures = Departure::all();
        if ($departures->count() == 0){
            return $this->onError(404, 'Departures Not Found');
        } else
        {
            return $this->onSuccess($departures, 'Ok');
        }
    }

    public function store(Request $request): JsonResponse {
        if ($this->isDeliveryServices($request->user())) {
            $fields = $request->validate([
                'content_name' => 'required|string'
            ]);
            $departure = Departure::create([
                'content_name' => $fields['content_name'],
                'status' => 'created'
            ]);
            return $this->onSuccess($departure, 'Departure Created');
        }
        return $this->onError(401, 'Unauthorized');
    }

    public function show($id): JsonResponse {
        $departure = Departure::find($id);
        if ($departure === null)
        {
            return $this->onError(404, 'Departure Not Found');
        } else
        {
            return $this->onSuccess($departure, 'OK');
        }
    }

    public function update(Request $request, $id): JsonResponse {
        if ($this->isDeliveryServices($request->user()) || $this->isAdmin($request->user())) {
            $fields = $request->validate([
                'content_name' => 'required|string',
            ]);
            $departure = Departure::find($id);
            $departure->update([
                'content_name' => $fields['content_name'],
            ]);
            return $this->onSuccess($departure, 'Departure Updated');
        }
        if ($this->isOperator($request->user())) {
            $fields = $request->validate([
                'content_name' => 'required|string',
                'status'=>[
                    'required',
                    Rule::in(['placed', 'issued']),
                ],
            ]);
            $departure = Departure::find($id);
            $departure->update([
                'content_name' => $fields['content_name'],
                'status' => $fields['status'],
            ]);
            return $this->onSuccess($departure, 'Departure Updated');
        }
        return $this->onError(401, 'Unauthorized');
    }

    public function destroy(Request $request, $id): JsonResponse {
        if ($this->isDeliveryServices($request->user())) {
            $departure = Departure::find($id);
            if ($departure === null)
            {
                return $this->onError(404, 'Departure Not Found');
            } else
            {
                $departure->delete();
                return $this->onSuccess([], 'Departure Deleted');
            }
        }
        return $this->onError(401, 'Unauthorized');

    }
}
