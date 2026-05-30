<?php

namespace App\Filament\Resources\Cards\Pages;

use App\Filament\Resources\Cards\CardResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateCard extends CreateRecord
{
    protected static string $resource = CardResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        $slug = Str::slug($data['name']['fr'] ?? 'animation');
        $data = $this->renameAnimationFiles($data, $slug);

        return $data;
    }

    private function renameAnimationFiles(array $data, string $slug): array
    {
        $disk = Storage::disk('cards');

        foreach ([
            'drawn_animation_path' => 'drawn',
            'real_animation_path' => 'real',
            'sound_path' => 'sounds',
        ] as $field => $suffix) {
            if (! empty($data[$field])) {
                $oldPath = $data[$field];
                $ext = pathinfo($oldPath, PATHINFO_EXTENSION);
                $newName = "{$suffix}/{$slug}_{$suffix}_".now()->timestamp.".{$ext}";

                if ($disk->exists($oldPath) && $oldPath !== $newName) {
                    $disk->move($oldPath, $newName);
                }

                $data[$field] = $newName;
            }
        }

        return $data;
    }
}
