<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PromoController extends Controller
{
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'code'       => ['required','string','max:64'],
                'type'       => ['required', Rule::in(['percent','flat'])],
                'amount'     => ['required','numeric','min:1'],
                'min'        => ['nullable','numeric','min:0'],
                'expires_at' => ['nullable','date'],
                'active'     => ['nullable','boolean'],
            ]);

            $data['code']   = strtoupper(trim($data['code']));
            $data['min']    = (float) ($data['min'] ?? 0);
            $data['active'] = (bool)  ($data['active'] ?? true);
            $data['expires_at'] = !empty($data['expires_at'])
                ? Carbon::parse($data['expires_at'])->endOfDay()
                : null;

            $promo = Promo::updateOrCreate(
                ['code' => $data['code']],
                [
                    'type'       => $data['type'],
                    'amount'     => (float) $data['amount'],
                    'min'        => $data['min'],
                    'expires_at' => $data['expires_at'],
                    'active'     => $data['active'],
                ]
            );

            return response()->json(['ok' => true, 'promo' => $promo], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Server error',
                'exception' => $e->getMessage(),
            ], 500);
        }
    }
}
