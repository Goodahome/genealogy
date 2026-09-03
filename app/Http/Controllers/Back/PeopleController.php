<?php

declare(strict_types=1);

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Couple;
use App\Models\Person;
use Illuminate\View\View;

final class PeopleController extends Controller
{
    public function search(): View
    {
        return view('back.people.search');
    }

    public function birthdays(): View
    {
        $people = Person::query()
            ->whereNotNull('dob')
            ->orderByRaw("DATE_FORMAT(dob, '%m-%d')")
            ->orderBy('firstname')
            ->get(['id', 'firstname', 'surname', 'sex', 'dob', 'dod', 'yod']);

        $birthdays = $people->map(static fn (Person $person): array => [
            'id' => $person->id,
            'name' => $person->name,
            'sex' => $person->sex,
            'month' => (int) $person->dob->format('n'),
            'day' => (int) $person->dob->format('j'),
            'birth_year' => (int) $person->dob->format('Y'),
            'deceased' => $person->isDeceased(),
        ])->values();

        return view('back.people.birthdays', [
            'birthdays' => $birthdays,
        ]);
    }

    public function add(): View
    {
        abort_unless(auth()->user()->hasPermission('person:create'), 403, __('app.unauthorized_access'));

        return view('back.people.add.person');
    }

    public function show(Person $person): View
    {
        return view('back.people.show', ['person' => $person]);
    }

    public function ancestors(Person $person): View
    {
        return view('back.people.ancestors', ['person' => $person]);
    }

    public function descendants(Person $person): View
    {
        return view('back.people.descendants', ['person' => $person]);
    }

    public function chart(Person $person): View
    {
        return view('back.people.chart', ['person' => $person]);
    }

    public function clanLineage(): View
    {
        return view('back.people.clan-lineage');
    }

    public function history(Person $person): View
    {
        return view('back.people.history', ['person' => $person]);
    }

    public function datasheet(Person $person): View
    {
        return view('back.people.datasheet', ['person' => $person]);
    }

    public function addFather(Person $person): View
    {
        abort_unless(auth()->user()->hasPermission('person:create'), 403, __('app.unauthorized_access'));

        return view('back.people.add.father', ['person' => $person]);
    }

    public function addMother(Person $person): View
    {
        abort_unless(auth()->user()->hasPermission('person:create'), 403, __('app.unauthorized_access'));

        return view('back.people.add.mother', ['person' => $person]);
    }

    public function addChild(Person $person): View
    {
        abort_unless(auth()->user()->hasPermission('person:create'), 403, __('app.unauthorized_access'));

        return view('back.people.add.child', ['person' => $person]);
    }

    public function addPartner(Person $person): View
    {
        abort_unless(auth()->user()->hasPermission('couple:create'), 403, __('app.unauthorized_access'));

        return view('back.people.add.partner', ['person' => $person]);
    }

    public function editContact(Person $person): View
    {
        abort_unless(auth()->user()->hasPermission('person:update'), 403, __('app.unauthorized_access'));

        return view('back.people.edit.contact', ['person' => $person]);
    }

    public function editDeath(Person $person): View
    {
        abort_unless(auth()->user()->hasPermission('person:update'), 403, __('app.unauthorized_access'));

        return view('back.people.edit.death', ['person' => $person]);
    }

    public function editFamily(Person $person): View
    {
        abort_unless(auth()->user()->hasPermission('person:update'), 403, __('app.unauthorized_access'));

        return view('back.people.edit.family', ['person' => $person]);
    }

    public function editFiles(Person $person): View
    {
        abort_unless(auth()->user()->hasPermission('person:update'), 403, __('app.unauthorized_access'));

        return view('back.people.edit.files', ['person' => $person]);
    }

    public function editPhotos(Person $person): View
    {
        abort_unless(auth()->user()->hasPermission('person:update'), 403, __('app.unauthorized_access'));

        return view('back.people.edit.photos', ['person' => $person]);
    }

    public function editProfile(Person $person): View
    {
        abort_unless(auth()->user()->hasPermission('person:update'), 403, __('app.unauthorized_access'));

        return view('back.people.edit.profile', ['person' => $person]);
    }

    public function editPartner(Person $person, Couple $couple): View
    {
        abort_unless(auth()->user()->hasPermission('couple:update'), 403, __('app.unauthorized_access'));

        return view('back.people.edit.partner', ['person' => $person, 'couple' => $couple]);
    }
}
