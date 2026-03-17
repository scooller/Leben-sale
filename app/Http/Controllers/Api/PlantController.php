<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plant;
use App\Models\Proyecto;
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
            ->with(['proyecto', 'activeReservation', 'coverImageMedia', 'interiorImageMedia'])
            ->whereHas('proyecto', function ($projectQuery) {
                $projectQuery->where('is_active', true);
            }) // Solo plantas con proyecto activo asociado
            ->where('is_active', true); // Solo plantas activas

        $projectValues = $this->normalizeInputValues($request->input('salesforce_proyecto_id'));
        $projectIdValues = $this->normalizeInputValues($request->input('proyecto_id', $request->input('project_id')));
        $dormValues = $this->normalizeInputValues($request->input('programa'));
        $banosValues = $this->normalizeInputValues($request->input('programa2'));
        $available = $this->normalizeBoolean($request->input('disponible', $request->input('available')));

        // Filtros
        if (count($projectValues) > 0) {
            $query->whereIn('salesforce_proyecto_id', $projectValues);
        }

        if (count($projectIdValues) > 0) {
            $query->whereHas('proyecto', function ($projectQuery) use ($projectIdValues) {
                $projectQuery->whereIn('id', $projectIdValues);
            });
        }

        if ($available !== null) {
            if ($available) {
                $query->whereDoesntHave('activeReservation');
            } else {
                $query->whereHas('activeReservation');
            }
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
        $plants = $query->paginate($perPage)->through(function (Plant $plant): array {
            $payload = $plant->toArray();
            $payload['proyecto'] = $this->projectPayload($plant->proyecto);

            return $payload;
        });

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

    private function normalizeBoolean(mixed $value): ?bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value)) {
            return $value === 1;
        }

        if (is_string($value)) {
            $normalizedValue = strtolower(trim($value));

            if (in_array($normalizedValue, ['1', 'true', 'yes', 'si'], true)) {
                return true;
            }

            if (in_array($normalizedValue, ['0', 'false', 'no'], true)) {
                return false;
            }
        }

        return null;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $plant = Plant::query()
            ->with(['proyecto', 'activeReservation', 'coverImageMedia', 'interiorImageMedia'])
            ->whereHas('proyecto', function ($projectQuery) {
                $projectQuery->where('is_active', true);
            })
            ->findOrFail($id);

        $payload = $plant->toArray();
        $payload['proyecto'] = $this->projectPayload($plant->proyecto);

        return response()->json($payload);
    }

    private function projectPayload(?Proyecto $proyecto): ?array
    {
        if (! $proyecto) {
            return null;
        }

        return [
            'id' => $proyecto->id,
            'name' => $proyecto->name,
            'tipo' => $proyecto->tipo,
            'direccion' => $proyecto->direccion,
            'comuna' => $proyecto->comuna,
            'provincia' => $proyecto->provincia,
            'region' => $proyecto->region,
            'pagina_web' => $proyecto->pagina_web,
            'etapa' => $proyecto->etapa,
            'horario_atencion' => $proyecto->horario_atencion,
            'entrega_inmediata' => $proyecto->entrega_inmediata,
            'is_active' => $proyecto->is_active,
        ];
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
