<?php

declare(strict_types=1);

namespace App\Livewire\People;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use TallStackUi\Traits\Interactions;

final class Files extends Component
{
    use Interactions;
    use WithFileUploads;

    public $person;

    public bool $editing = false;

    public bool $canManage = false;

    public array $uploads = [];

    public Collection $files;

    protected $listeners = [
        'files_updated' => 'reloadFiles',
    ];

    public function mount(): void
    {
        $this->canManage = auth()->user()->hasPermission('person:update');
        $this->reloadFiles();
    }

    public function reloadFiles(): void
    {
        $this->files = $this->person->getMedia('files');
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
            'uploads.*' => 'file|max:10240|mimes:pdf,txt,doc,docx,xls,xlsx',
        ]);

        $files = collect($this->uploads)
            ->filter(fn ($file): bool => $file instanceof UploadedFile)
            ->values()
            ->all();

        if ($files === []) {
            return;
        }

        foreach ($files as $upload) {
            $this->person->addMedia($upload)->toMediaCollection('files', 'files');
        }

        $count = count($files);
        $this->uploads = [];
        $this->reloadFiles();
        $this->dispatch('files_updated');

        $this->toast()->success(__('app.save'), trans_choice('person.files_saved', $count))->send();
    }

    public function deleteFile(int $id): void
    {
        $this->ensureCanManage();

        $file = $this->files->firstWhere('id', $id);

        if (! $file) {
            return;
        }

        $file->delete();
        $this->reorderFiles();
        $this->reloadFiles();
        $this->dispatch('files_updated');

        $this->toast()->success(__('app.delete'), __('person.file_deleted'))->send();
    }

    public function moveFile(int $position, string $direction): void
    {
        $this->ensureCanManage();

        $targetPosition = $direction === 'up' ? $position - 1 : $position + 1;

        $this->files->transform(function ($file) use ($position, $targetPosition) {
            if ($file->order_column === $position) {
                $file->order_column = $targetPosition;
            } elseif ($file->order_column === $targetPosition) {
                $file->order_column = $position;
            }

            $file->save();

            return $file;
        });

        $this->reloadFiles();
        $this->dispatch('files_updated');
    }

    public function render(): View
    {
        return view('livewire.people.files');
    }

    private function ensureCanManage(): void
    {
        abort_unless($this->canManage || auth()->user()->hasPermission('person:update'), 403, __('app.unauthorized_access'));

        $this->canManage = true;
    }

    private function reorderFiles(): void
    {
        if ($this->files->isEmpty()) {
            return;
        }

        $ordered = $this->files->sortBy('order_column')->pluck('id')->toArray();

        Media::setNewOrder($ordered);
    }
}
