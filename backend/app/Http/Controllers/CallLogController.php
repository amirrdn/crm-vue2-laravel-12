<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CallLogService;
use Illuminate\Validation\ValidationException;

class CallLogController extends Controller
{
    protected $callLogService;

    public function __construct(CallLogService $callLogService)
    {
        $this->callLogService = $callLogService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['contact_id', 'startDate', 'endDate']);
        
        // Convert contact_id to integer if present
        if (isset($filters['contact_id'])) {
            $filters['contact_id'] = (int) $filters['contact_id'];
        }

        return response()->json($this->callLogService->getAll($filters));
    }

    public function show($id)
    {
        $callLog = $this->callLogService->getById($id);
        return response()->json($callLog);
    }

    public function store(Request $request)
    {
        try {
            $result = $this->callLogService->create($request->all());
            return response()->json($result, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $result = $this->callLogService->update($id, $request->all());
            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function destroy($id)
    {
        $this->callLogService->delete($id);
        return response()->json(['message' => 'Call log successfully deleted']);
    }

    public function stream(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'contact_id' => 'required|exists:contacts,id',
                'phone' => 'required|string|max:20',
            ], [
                'contact_id.required' => 'Contact ID is required',
                'contact_id.exists' => 'Contact not found',
                'phone.required' => 'Phone number is required',
                'phone.max' => 'Phone number cannot exceed 20 characters',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $status = ['completed', 'missed', 'no_answer'][rand(0, 2)];
            $duration = $status === 'completed' ? rand(30, 300) : 0;

            $callLog = $this->callLogService->create([
                'contact_id' => $request->contact_id,
                'started_at' => now(),
                'duration' => $duration,
                'status' => $status
            ]);

            return response()->json($callLog);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while processing the call',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 