<?php

namespace App\Livewire;

use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;

class CitizenshipDetails extends MyProfileComponent
{
    public array $only = ['citizenship_number', 'citizenship_issue_date', 'citizenship_image_path'];
    public array $data;
    public $user;
    public $userClass;

    public static $sort = 12;

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
                TextInput::make('citizenship_number')
                    ->required()
                    ->unique(ignoreRecord: true),
                DatePicker::make('citizenship_issue_date')
                    ->required()
                    ->native(false)
                    ->displayFormat('d F Y')
                    ->weekStartsOnSunday()
                    ->closeOnDateSelection(),
                FileUpload::make('citizenship_image_path')
                    ->label('Citizenship Scanned Photo')
                    ->hint('Document should be properly scanned.')
                    ->image()
                    ->directory('images/citizenships')
                    ->required()
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
        return view('livewire.citizenship-details');
    }
}
