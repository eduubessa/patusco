<?php

declare(strict_types=1);

namespace App\Http\Controllers\Animal;

use App\Actions\Animal\CreateNewAnimal;
use App\Http\Controllers\Controller;
use App\Http\Requests\Animal\StoreAnimalRequest;
use App\Models\Animal;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Log;
use Throwable;

final class StoreAnimalController extends Controller
{
    use AuthorizesRequests;

    /**
     * Handle the incoming request.
     */
    public function __invoke(StoreAnimalRequest $request, CreateNewAnimal $action)
    {
        //
        $this->authorize('create', Animal::class);

        try {

            $animal = $action->handle($request->validated(), auth()->user());

            return redirect()
                ->route('animals.show', $animal)
                ->with('success', 'Animal criado com sucesso.');

        } catch (Throwable $e) {
            Log::error("Animal store animal failed. Message: {$e->getMessage()}");

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Animal store animal failed.');
        }
    }
}
