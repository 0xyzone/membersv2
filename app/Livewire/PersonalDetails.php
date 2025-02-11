<?php

namespace App\Livewire;

use Filament\Forms\Components\Textarea;
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
    public array $only = ['username', 'gender', 'date_of_birth', 'primary_contact_number', 'secondary_contact_number', 'permanent_address', 'current_address'];
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
                TextInput::make('primary_contact_number')
                    ->numeric()
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(10),
                TextInput::make('secondary_contact_number')
                    ->numeric()
                    ->maxLength(10),
                Textarea::make('permanent_address')
                    ->hint('Full Address written in your document.')
                    ->placeholder('Dallu Awas, Kathmandu, Nepal')
                    ->autosize()
                    ->required(),
                Textarea::make('current_address')
                    ->hint('Full Address where you currently reside.')
                    ->placeholder('Dallu Awas, Kathmandu, Nepal')
                    ->autosize()
                    ->required(),
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
