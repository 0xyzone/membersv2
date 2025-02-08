<?php

namespace App\Livewire;

use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;

class PersonalDetails extends MyProfileComponent
{
    public array $only = ['username', 'gender', 'date_of_birth'];
    public array $data;
    public $user;
    public $userClass;

    public static $sort = 11;

    public function mount()
    {
        $this->user = Filament::getCurrentPanel()->auth()->user();
        $this->userClass = get_class($this->user);

        $this->form->fill($this->user->only($this->only));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('username')
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('gender')
                    ->required()
                    ->options([
                        'female' => 'Female',
                        'male' => 'Male',
                        'others' => 'Others',
                    ]),
                DatePicker::make('date_of_birth')
                    ->required()
                    ->native(false)
                    ->displayFormat('d F Y')
                    ->weekStartsOnSunday()
                    ->closeOnDateSelection()
                    ->beforeOrEqual(now())
                    ->maxDate(now()->subYears(16)),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = collect($this->form->getState())->only($this->only)->all();
        $this->user->update($data);
        Notification::make()
            ->success()
            ->title(__('Profile updated successfully'))
            ->send();
    }

    public function render()
    {
        return view('livewire.personal-details');
    }
}
