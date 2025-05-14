<?php

declare(strict_types=1);

namespace App\Livewire\People\Add;

use App\Livewire\Forms\People\PersonForm;
use App\Livewire\Traits\TrimStringsAndConvertEmptyStringsToNull;
use App\PersonPhotos;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use TallStackUi\Traits\Interactions;

final class Person extends Component
{
    use Interactions;
    use TrimStringsAndConvertEmptyStringsToNull;
    use WithFileUploads;

    // -----------------------------------------------------------------------
    public PersonForm $personForm;

    public array $photos = [];

    public array $backup = [];

    // -----------------------------------------------------------------------
    public function mount(): void
    {
        $this->personForm->firstname = null;
        $this->personForm->surname   = null;
        $this->personForm->birthname = null;
        $this->personForm->nickname  = null;

        $this->personForm->sex       = null;
        $this->personForm->gender_id = null;

        $this->personForm->yob = null;
        $this->personForm->dob = null;
        $this->personForm->pob = null;

        $this->personForm->photo = null;

        // 初始化农历出生日期
        $this->personForm->dob_year = null;
        $this->personForm->dob_month = null;
        $this->personForm->dob_day = null;
        $this->personForm->dob_leap = false;
    }

    public function deleteUpload(array $content): void
    {
        /* the $content contains:
            [
                'temporary_name',
                'real_name',
                'extension',
                'size',
                'path',
                'url',
            ]
        */

        if (empty($this->uploads)) {
            return;
        }

        $this->uploads = collect($this->uploads)
            ->filter(fn (UploadedFile $file): bool => $file->getFilename() !== $content['temporary_name'])
            ->values()
            ->toArray();

        rescue(
            fn () => UploadedFile::deleteTemporaryFile($content['temporary_name']),
            report: false
        );
    }

    public function updatingUploads(): void
    {
        $this->backup = $this->uploads;
    }

    public function updatedUploads(): void
    {
        if (empty($this->uploads)) {
            return;
        }

        $this->uploads = collect(array_merge($this->backup, (array) $this->uploads))
            ->unique(fn (UploadedFile $file): string => $file->getClientOriginalName())
            ->toArray();
    }

    public function savePerson(): void
    {
        if ($this->isDirty()) {
            $validated = $this->personForm->validate();
            
            // 创建新的人物记录
            $new_person = \App\Models\Person::create([
                'firstname' => $validated['firstname'],
                'surname'   => $validated['surname'],
                'birthname' => $validated['birthname'],
                'nickname'  => $validated['nickname'],
                'sex'       => $validated['sex'],
                'gender_id' => $validated['gender_id'] ?? null,
                'yob'       => $validated['yob'],
                
                // 保存农历日期字段
                'dob_lunar_year' => $validated['dob_year'] ?? null,
                'dob_lunar_month' => $validated['dob_month'] ?? null,
                'dob_lunar_day' => $validated['dob_day'] ?? null,
                'dob_lunar_leap' => $validated['dob_leap'] ?? false,
                
                'pob'       => $validated['pob'],
                'yod'       => $validated['yod'] ?? null,
                'pod'       => $validated['pod'] ?? null,
                'team_id'   => auth()->user()->currentTeam->id,
            ]);
            
            if ($this->photos) {
                $personPhotos = new PersonPhotos($new_person);
                $personPhotos->save($this->photos);
            }

            $this->toast()->success(__('app.save'), $new_person->name . ' ' . __('app.created'))->flash()->send();

            $this->redirect('/people/' . $new_person->id);
        }
    }

    public function resetPerson(): void
    {
        $this->mount();
    }

    public function isDirty(): bool
    {
        return
        $this->personForm->firstname !== null or
        $this->personForm->surname !== null or
        $this->personForm->birthname !== null or
        $this->personForm->nickname !== null or

        $this->personForm->sex !== null or
        $this->personForm->gender_id !== null or

        $this->personForm->yob !== null or
        $this->personForm->dob !== null or

        $this->personForm->dob_year !== $this->person->dob_lunar_year or
        $this->personForm->dob_month !== $this->person->dob_lunar_month or
        $this->personForm->dob_day !== $this->person->dob_lunar_day or
        $this->personForm->dob_leap !== $this->person->dob_lunar_leap or

        $this->personForm->pob !== null;
    }

    // ------------------------------------------------------------------------------
    public function render(): View
    {
        return view('livewire.people.add.person');
    }
}
