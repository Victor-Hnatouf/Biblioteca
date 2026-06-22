<?php
namespace App\Observers;
use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
class ActivityLogObserver
{
    public function created(Model $model): void
    {
        $this->registar($model, 'criado', null);
    }
    public function updated(Model $model): void
    {
        $dirty = collect($model->getDirty())
            ->except(['updated_at', 'created_at', 'remember_token'])
            ->mapWithKeys(function ($novoValor, $campo) use ($model) {
                return [$campo => [
                    'antes' => $model->getOriginal($campo),
                    'depois' => $novoValor,
                ]];
            })
            ->toArray();
        if (empty($dirty)) {
            return;
        }
        $this->registar($model, 'atualizado', $dirty);
    }
    public function deleted(Model $model): void
    {
        $this->registar($model, 'eliminado', null);
    }
    private function registar(Model $model, string $evento, ?array $alteracoes): void
    {
        if ($model instanceof ActivityLog) {
            return;
        }
        ActivityLog::registar(
            modulo: class_basename($model),
            objetoId: $model->getKey(),
            evento: $evento,
            alteracoes: $alteracoes,
        );
    }
}
