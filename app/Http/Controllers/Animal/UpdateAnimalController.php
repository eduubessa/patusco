<?php

namespace App\Http\Controllers\Animal;

use App\Actions\Animal\UpdateAnimal;
use App\Http\Controllers\Controller;
use App\Http\Requests\Animal\UpdateAnimalRequest;
use App\Models\Animal;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class UpdateAnimalController extends Controller
{
    use AuthorizesRequests;

    /**
     * Handle the incoming request.
     */
    public function __invoke(UpdateAnimalRequest $request, UpdateAnimal $action, Animal $animal)
    {
        //
        $this->authorize('update', $animal);

        try {

            $action->handle($animal, $request->validated());

            return redirect()
                ->route('animals.show', $animal)
                ->with('success', 'O animal foi atualizado com sucesso.');

        }catch(\Throwable $e){
            \Log::error("Update animal failed: {$e->getMessage()}");

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Não foi possivel atualizar o animal.');
        }
    }
}
