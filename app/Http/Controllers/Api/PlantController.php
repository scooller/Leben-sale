<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Plant::query()
            ->with(['proyecto', 'activeReservation'])
            ->whereHas('proyecto') // Solo plantas con proyecto asociado
            ->where('is_active', true); // Solo plantas activas

        $projectValues = $this->normalizeInputValues($request->input('salesforce_proyecto_id'));
        $dormValues = $this->normalizeInputValues($request->input('programa'));
        $banosValues = $this->normalizeInputValues($request->input('programa2'));

        // Filtros
        if (count($projectValues) > 0) {
            $query->whereIn('salesforce_proyecto_id', $projectValues);
        }

        if (count($dormValues) > 0 || count($banosValues) > 0) {
            $query->where(function ($subQuery) use ($dormValues, $banosValues) {
                $normalizedColumn = "REPLACE(programa, ' ', '')";

                if (count($dormValues) > 0) {
                    $subQuery->where(function ($dormQuery) use ($normalizedColumn, $dormValues) {
                        foreach ($dormValues as $dormValue) {
                            $dormText = strtoupper((string) $dormValue);
                            $dormNumber = preg_replace('/\D+/', '', $dormText);

                            if ($dormText === 'ST') {
                                $dormQuery->orWhereRaw($normalizedColumn.' like ?', ['ST%']);
                            } elseif ($dormNumber !== '') {
                                $dormQuery->orWhereRaw($normalizedColumn.' like ?', [$dormNumber.'D%']);
                            }
                        }
                    });
                }

                if (count($banosValues) > 0) {
                    $subQuery->where(function ($banosQuery) use ($normalizedColumn, $banosValues) {
                        foreach ($banosValues as $banosValue) {
                            $banosNumber = preg_replace('/\D+/', '', (string) $banosValue);

                            if ($banosNumber !== '') {
                                $banosQuery
                                    ->orWhereRaw($normalizedColumn.' like ?', ['%+'.$banosNumber.'B%'])
                                    ->orWhereRaw($normalizedColumn.' like ?', ['%+'.$banosNumber]);
                            }
                        }
                    });
                }
            });
        }

        if ($request->has('min_precio')) {
            $query->where('precio_base', '>=', $request->min_precio);
        }

        if ($request->has('max_precio')) {
            $query->where('precio_base', '<=', $request->max_precio);
        }

        // Obtener perPage del request o usar 12 por defecto
        $perPage = $request->get('perPage', 12);
        $plants = $query->paginate($perPage);

        return response()->json($plants);
    }

    /**
     * @return list<string>
     */
    private function normalizeInputValues(mixed $value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map(static fn (mixed $item): string => trim((string) $item), $value), static fn (string $item): bool => $item !== ''));
        }

        if (is_string($value)) {
            if ($value === '') {
                return [];
            }

            if (str_contains($value, ',')) {
                $parts = explode(',', $value);

                return array_values(array_filter(array_map(static fn (string $item): string => trim($item), $parts), static fn (string $item): bool => $item !== ''));
            }

            return [trim($value)];
        }

        if ($value === null) {
            return [];
        }

        return [trim((string) $value)];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $plant = Plant::with(['proyecto', 'activeReservation'])->findOrFail($id);

        return response()->json($plant);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
