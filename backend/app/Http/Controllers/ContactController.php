<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ContactService;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    protected $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
            'position' => $request->input('position'),
            'favorites_only' => $request->boolean('favorites_only')
        ];
        
        $contacts = $this->contactService->getAll($filters);
        return response()->json($contacts);
    }

    public function show($id)
    {
        try {
            $contact = $this->contactService->getById($id);
            return response()->json($contact);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Contact not found'], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $contact = $this->contactService->create($request->all());
            return response()->json($contact, 201);
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
            $contact = $this->contactService->update($id, $request->all());
            return response()->json($contact);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function destroy($id)
    {
        $this->contactService->delete($id);
        return response()->json(['message' => 'Contact successfully deleted']);
    }

    public function toggleFavorite($id)
    {
        $contact = $this->contactService->toggleFavorite($id);
        return response()->json($contact);
    }

    public function getPositions()
    {
        $positions = $this->contactService->getUniquePositions();
        return response()->json($positions);
    }
} 