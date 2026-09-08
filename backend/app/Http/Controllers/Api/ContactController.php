<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactEnquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'category' => ['nullable', 'string', 'in:General,Booking,Media'],
            'message' => ['required', 'string'],
        ]);

        $enquiry = ContactEnquiry::create($data + ['category' => $data['category'] ?? 'General']);

        return response()->json(['data' => $enquiry], 201);
    }
}
