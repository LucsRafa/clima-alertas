<?php

namespace App\Http\Controllers;

use App\Http\Requests\Alert\StoreAlertRequest;
use App\Models\Alert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    /**
     * List user alerts.
     */
    public function index(Request $request): JsonResponse
    {
        $alerts = Alert::with('city')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('notify_at')
            ->paginate((int) $request->integer('per_page', 15));

        return response()->json($alerts);
    }

    /**
     * Create a new alert for user.
     */
    public function store(StoreAlertRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $alert = Alert::create($data);
        return response()->json($alert, 201);
    }

    /**
     * Delete an alert if it belongs to user.
     */
    public function destroy(Request $request, Alert $alert): JsonResponse
    {
        abort_unless($alert->user_id === $request->user()->id, 403);
        $alert->delete();
        return response()->json(null, 204);
    }
}

