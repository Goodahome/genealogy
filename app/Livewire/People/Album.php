<?php

declare(strict_types=1);

namespace App\Livewire\People;

use App\PersonPhotos;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use TallStackUi\Traits\Interactions;

final class Album extends Component
{
    use Interactions;
    use WithFileUploads;

    public $person;

    public bool $editing = false;

    public bool $canManage = false;

    public array $uploads = [];

    public Collection $photos;

    public function mount(): void
    {
        $this->canManage = auth()->user()->hasPermission('person:update');
        $this->loadPhotos();
    }

    public function toggleEditing(): void
    {
        $this->ensureCanManage();

        $this->editing = ! $this->editing;
    }

    public function updatedUploads(): void
    {
        $this->ensureCanManage();

        if (empty($this->uploads)) {
            return;
        }

        $this->validate([
            'uploads.*' => 'image|max:10240',
        ]);

        $files = collect($this->uploads)
            ->filter(fn ($file): bool => $file instanceof UploadedFile)
            ->values()
            ->all();

        if ($files === []) {
            return;
        }

        (new PersonPhotos($this->person))->save($files);

        $this->uploads = [];
        $this->person->refresh();
        $this->loadPhotos();
        $this->dispatch('photos_updated');

        $this->toast()->success(__('app.save'), trans_choice('person.photos_saved', count($files)))->send();
    }

    public function deletePhoto(string $photo): void
    {
        $this->ensureCanManage();

        $this->deletePhotoFiles($photo, (int) $this->person->team_id);

        if ($photo === $this->person->photo) {
            $this->setNewPrimaryPhoto();
        }

        $this->person->refresh();
        $this->loadPhotos();
        $this->dispatch('photos_updated');

        $this->toast()->success(__('app.delete'), __('person.photo_deleted'))->send();
    }

    public function setPrimary(string $photo): void
    {
        $this->ensureCanManage();

        $this->person->update(['photo' => $photo]);
        $this->person->refresh();
        $this->loadPhotos();
        $this->dispatch('photos_updated');
    }

    public function render(): View
    {
        return view('livewire.people.album');
    }

    private function ensureCanManage(): void
    {
        abort_unless($this->canManage || auth()->user()->hasPermission('person:update'), 403, __('app.unauthorized_access'));

        $this->canManage = true;
    }

    private function loadPhotos(): void
    {
        $path = public_path('storage/photos/' . $this->person->team_id);

        if (! is_dir($path)) {
            $this->photos = collect();

            return;
        }

        $this->photos = collect(
            Finder::create()
                ->in($path)
                ->name("{$this->person->id}_*.{webp,gif}")
        )
            ->map(fn (SplFileInfo $file): array => $this->mapPhotoData($file))
            ->sortBy('name')
            ->values();
    }

    private function mapPhotoData(SplFileInfo $file): array
    {
        $teamId = $this->person->team_id;
        $filename = $file->getFilename();

        return [
            'name' => $filename,
            'url' => Storage::url("photos-384/{$teamId}/{$filename}"),
            'url_original' => Storage::url("photos/{$teamId}/{$filename}"),
            'is_primary' => $filename === $this->person->photo,
        ];
    }

    private function setNewPrimaryPhoto(): void
    {
        $files = File::glob(
            public_path("storage/photos/{$this->person->team_id}/{$this->person->id}_*.{webp,gif}"),
            GLOB_BRACE
        );

        $this->person->update([
            'photo' => $files ? basename((string) $files[0]) : null,
        ]);
    }

    private function deletePhotoFiles(string $photo, int $teamId): void
    {
        foreach (config('app.photo_folders') as $folder) {
            Storage::disk($folder)->delete("{$teamId}/{$photo}");
        }
    }
}
