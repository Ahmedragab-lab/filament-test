<?php

namespace App\Filament\Pages;

use App\Models\Company;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class EditCompany extends Page  implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.edit-company';

    public ?array $data = []; 
    public function mount(): void 
    {
        $this->form->fill();
        // $this->form->fill(auth()->user()->company->attributesToArray()); 
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
            ])
            ->statePath('data');
    } 
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }
    public function save(): void
    {
        try {
            // dd($this->form->getState());
            $data = $this->form->getState();
            Company::updateOrCreate([
                'user_id' => Auth::user()->id
            ],[
                'name' => $data['name'],
                'user_id' => Auth::user()->id
            ]);
        } catch (Halt $exception) {
            return;
        }
        Notification::make() 
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send(); 
    }
}
