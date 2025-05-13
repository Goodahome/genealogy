<?php

declare(strict_types=1);

namespace App\Livewire\People\Edit;

use App\Livewire\Forms\People\ProfileForm;
use App\Livewire\Traits\TrimStringsAndConvertEmptyStringsToNull;
use App\Models\Person;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

final class Profile extends Component
{
    use Interactions;
    use TrimStringsAndConvertEmptyStringsToNull;

    // -----------------------------------------------------------------------
    public Person $person;

    public ProfileForm $profileForm;

    // -----------------------------------------------------------------------
    public function mount(): void
    {
        $this->profileForm->firstname = $this->person->firstname;
        $this->profileForm->surname   = $this->person->surname;
        $this->profileForm->birthname = $this->person->birthname;
        $this->profileForm->nickname  = $this->person->nickname;

        $this->profileForm->sex       = $this->person->sex;
        $this->profileForm->gender_id = $this->person->gender_id;

        $this->profileForm->yob = $this->person->yob ?? null;
        $this->profileForm->dob = $this->person->dob ? Carbon::parse($this->person->dob)->format('Y-m-d') : null;
        $this->profileForm->pob = $this->person->pob;

        $this->profileForm->summary = $this->person->summary;
        
        // 初始化农历出生日期
        $this->profileForm->dob_year = $this->person->dob_lunar_year;
        $this->profileForm->dob_month = $this->person->dob_lunar_month;
        $this->profileForm->dob_day = $this->person->dob_lunar_day;
        $this->profileForm->dob_leap = (bool)$this->person->dob_lunar_leap;

        // 初始化农历死亡日期
        $this->profileForm->dod_year = $this->person->dod_lunar_year;
        $this->profileForm->dod_month = $this->person->dod_lunar_month;
        $this->profileForm->dod_day = $this->person->dod_lunar_day;
        $this->profileForm->dod_leap = (bool)$this->person->dod_lunar_leap;
    }

    public function saveProfile(): void
    {
        if ($this->isDirty()) {
            $validated = $this->profileForm->validate();

            $this->person->update([
                'firstname' => $validated['firstname'] ?? null,
                'surname' => $validated['surname'] ?? null,
                'birthname' => $validated['birthname'] ?? null,
                'nickname' => $validated['nickname'] ?? null,
                'sex' => $validated['sex'] ?? null,
                'gender_id' => $validated['gender_id'] ?? null,
                'yob' => $validated['yob'] ?? null,
                'dob' => $validated['dob'] ?? null,
                'pob' => $validated['pob'] ?? null,
                'summary' => $validated['summary'] ?? null,
                'dob_lunar_year' => $validated['dob_year'] ?? null,
                'dob_lunar_month' => $validated['dob_month'] ?? null,
                'dob_lunar_day' => $validated['dob_day'] ?? null,
                'dob_lunar_leap' => $validated['dob_leap'] ?? false,
                'dod_lunar_year' => $validated['dod_year'] ?? null,
                'dod_lunar_month' => $validated['dod_month'] ?? null,
                'dod_lunar_day' => $validated['dod_day'] ?? null,
                'dod_lunar_leap' => $validated['dod_leap'] ?? false,
            ]);

            $this->toast()->success(__('app.save'), __('app.saved'))->flash()->send();

            $this->redirect('/people/' . $this->person->id);
        }
    }

    public function resetProfile(): void
    {
        $this->mount();
    }

    public function isDirty(): bool
    {
        return
        $this->profileForm->firstname !== $this->person->firstname or
        $this->profileForm->surname !== $this->person->surname or
        $this->profileForm->birthname !== $this->person->birthname or
        $this->profileForm->nickname !== $this->person->nickname or

        $this->profileForm->sex !== $this->person->sex or
        $this->profileForm->gender_id !== $this->person->gender_id or

        $this->profileForm->yob !== $this->person->yob or
        $this->profileForm->dob !== ($this->person->dob ? Carbon::parse($this->person->dob)->format('Y-m-d') : null) or
        $this->profileForm->pob !== $this->person->pob or

        $this->profileForm->dob_year !== $this->person->dob_lunar_year or
        $this->profileForm->dob_month !== $this->person->dob_lunar_month or
        $this->profileForm->dob_day !== $this->person->dob_lunar_day or
        $this->profileForm->dob_leap !== $this->person->dob_lunar_leap or
        $this->profileForm->dod_year !== $this->person->dod_lunar_year or
        $this->profileForm->dod_month !== $this->person->dod_lunar_month or
        $this->profileForm->dod_day !== $this->person->dod_lunar_day or
        $this->profileForm->dod_leap !== $this->person->dod_lunar_leap or

        $this->profileForm->summary !== $this->person->summary;
    }

    // ------------------------------------------------------------------------------
    public function render(): View
    {
        return view('livewire.people.edit.profile');
    }
}
