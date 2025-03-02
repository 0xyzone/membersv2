<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Forms\Components\Radio;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;

class CitizenshipDetails extends MyProfileComponent
{
    public array $only = ['verification_document_number', 'verification_document_type', 'verification_document_issue_date', 'verification_document_expiry_date', 'verification_document_image_path'];
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
        ->model(User::class)
            ->schema([
                Radio::make('verification_document_type')
                ->options([
                    "passport" => "Passport",
                    "nid" => "NID",
                    "citizenship" => "Citizenship"
                ])
                ->inline()
                ->required(),
                TextInput::make('verification_document_number')
                    ->required()
                    ->unique(ignorable: $this->user),
                DatePicker::make('verification_document_issue_date')
                    ->required()
                    ->native(false)
                    ->displayFormat('d F Y')
                    ->weekStartsOnSunday()
                    ->closeOnDateSelection(),
                DatePicker::make('verification_document_expiry_date')
                ->hint('If available')
                    ->native(false)
                    ->displayFormat('d F Y')
                    ->weekStartsOnSunday()
                    ->closeOnDateSelection(),
                FileUpload::make('verification_document_image_path')
                    ->label('Verfication Document Photo')
                    ->hint('Document should be properly scanned.')
                    ->image()
                    ->directory('images/verification_documents')
                    ->required()
                    ->moveFile()
                    ->deletable()
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
